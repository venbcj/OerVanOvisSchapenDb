<?php

namespace Utils\Rector\Rector;

use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Return_;
use Rector\NodeTypeResolver\Node\AttributeKey;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Modifiers;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Scalar\InterpolatedString;
use PhpParser\Node\InterpolatedStringPart;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;

trait ArgSplitter {

    private function splitArgsFromString($expr) {
        $parts = [$expr];
        if ($expr instanceof Concat) {
            $parts = $this->flattenConcat($expr);
        }
        $parts = $this->flattenInterpolations($parts);
        $sql = '';
        $args = [];
        $func_args = [];
        $i = 1;
        foreach ($parts as $part) {
            if ($part instanceof String_ || $part instanceof InterpolatedStringPart) {
                $sql .= $part->value;
            } else {
                $variable = $this->unwrapFunctionCall($part);
                $param = ':' . $variable->name;
                $sql .= $param;
                $func_args[$variable->name] = $variable;
                $arg = [$param, $variable];
                if ($this->canInferTypeFromName($variable)) {
                    $arg[] = $this->inferTypeFrom($variable);
                }
                $args[$variable->name] = $arg;
            }
        }
        $sql = $this->unquotePlaceholders($sql);
        return [$sql, array_values($args), array_values($func_args)];
    }

    private function unquotePlaceholders($sql) {
        return preg_replace("#'(:[a-zA-Z_][_a-zA-Z0-9]*)'#", '\1', $sql);
    }

    private function looksLikeString($query) {
        return
            $query instanceof Concat
            || $query instanceof InterpolatedString
            # hier wordt het wel heel traag van. Eindeloze lus?
            # || $query instanceof String_
            ;
    }

    private function looksLikeQuery($sql) {
        return preg_match('#^\s*(SELECT|INSERT INTO|DELETE FROM|UPDATE)#', $sql);
    }

    // TODO: heuristiekjes
    // *Id -> int
    // *at -> int ("aantal")
    // dm* -> date
    // *dm -> date
    private function canInferTypeFromName(Node $variable): bool {
        $name = $variable->name;
        return in_array(substr($name, -2), ['Id', 'dm', 'at'])
            || substr($name, 0, 2) == 'dm';
    }

    // TODO: zoiets als self::INT
    private function inferTypeFrom(Node $variable): Node {
        $name = $variable->name;
        switch (substr($name, -2)) {
        case 'Id':
        case 'at':
            return new ClassConstFetch(new Name('self'), new Identifier('INT'));
        case 'dm':
            return new ClassConstFetch(new Name('self'), new Identifier('DATE'));
        default:
            if (substr($name, 0, 2) == 'dm') {
                return new ClassConstFetch(new Name('self'), new Identifier('DATE'));
            }
            throw new \Exception("Programmer error. Promise 'can infer', then infer. Name in violation: $name");
        }
    }

    // dit 'tbl'-prefix maakt het wel HEEL SPECIFIEK
    private function guess_gateway_name($sql) {
        if (preg_match('#(FROM|INTO|UPDATE) tbl([a-zA-Z0-9]*)#', $sql, $matches)) {
            $guess = strtolower($matches[2]);
            if ($guess == 'leden') {
                // keuzes, keuzes
                $guess = 'lid';
            }
            return $guess;
        }
        return 'TODO';
    }

    private function flattenConcat(Concat $concat): array {
        return array_merge(
            $concat->left instanceof Concat ? $this->flattenConcat($concat->left) : [$concat->left],
            $concat->right instanceof Concat ? $this->flattenConcat($concat->right) : [$concat->right]
        );
    }

    private function flattenInterpolations(array $parts) {
        $res = [];
        foreach ($parts as $part) {
            if ($part instanceof InterpolatedString) {
                foreach ($part->parts as $inner_part) {
                    $res[] = $inner_part;
                }
            } else {
                $res[] = $part;
            }
        }
        return $res;
    }

    private function unwrapFunctionCall(Node $expr): Node {
        if ($this->isEscape($expr)) {
            return $expr->args[1]->value; // de variabele die ge-escaped werd
        }
        if ($this->isDbNullInput($expr)) {
            return $expr->args[0]->value;
        }
        return $expr;
    }

    private function isEscape($expr) {
        return
            $expr instanceof Node\Expr\FuncCall
            && $expr->name instanceof Node\Name
            && $expr->name->toString() === 'mysqli_real_escape_string'
            && isset($expr->args[1]);
    }

    private function isDbNullInput($expr) {
        return
            $expr instanceof Node\Expr\FuncCall
            && $expr->name instanceof Node\Name
            && $expr->name->toString() === 'db_null_input'
            && isset($expr->args[0]);
    }

    private function composeDeclaration($gateway) {
        return new Expression(new Assign(
            new Variable($this->name_var($gateway)),
            new New_(new Name($this->name_class($gateway)))
        ));
    }

    private function name_var($gateway) {
        return strtolower($gateway) . '_gateway';
    }

    private function name_class($gateway) {
        return ucfirst(strtolower($gateway)) . 'Gateway';
    }

    private function composeCall(Node $assign, string $gateway, $func_args) {
        $call_args = [];
        foreach ($func_args as $arg) {
            $call_args[] = new Arg($arg);
        }
        return new Expression(new Assign(
            $assign->var,
            new MethodCall(
                new Variable($this->name_var($gateway)),
                $assign->var->name,
                $call_args
            ),
        ));
    }

    private function composeMethod($assign, $func_args, $sql, $args) {
        return new ClassMethod(
            $assign->var->name,
            [
                'flags' => Modifiers::PUBLIC,
                'params' => $func_args,
                'stmts' => [
                    new Expression(new Assign(
                        new Variable('sql'),
                        new String_($sql, [
                            AttributeKey::KIND => String_::KIND_HEREDOC,
                            AttributeKey::DOC_LABEL => 'SQL',
                        ])
                    )),
                    new Expression(new Assign(
                        new Variable('args'),
                        $this->nodeFactory->createArray($args)
                    )),
                    new Return_(
                        new MethodCall(
                            new Variable('this'),
                            'run_query',
                            [
                                new Arg(new Variable('sql')),
                                new Arg(new Variable('args'))
                            ]
                        )
                    )
                ]
            ]
        );
    }

}
