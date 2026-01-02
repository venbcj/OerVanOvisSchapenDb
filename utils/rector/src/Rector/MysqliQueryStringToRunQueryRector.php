<?php

declare(strict_types=1);

namespace Utils\Rector\Rector;

use PhpParser\Node\Scalar\InterpolatedString;
use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class MysqliQueryStringToRunQueryRector extends AbstractRector {

    use ArgSplitter;

    public function getRuleDefinition(): RuleDefinition {
        return new RuleDefinition(
            'change mysqli_query(db, statement) into this->run_query(sql, arguments)',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
// @todo fill code before
CODE_SAMPLE
        ,
            <<<'CODE_SAMPLE'
// @todo fill code after
CODE_SAMPLE
        ),
            ]);
    }

    public function getNodeTypes(): array {
        return [Expression::class];
    }

    public function refactor(Node $node) {
        if (! $node instanceof Expression) {
            return null;
        }
        if (! $node->expr instanceof Assign) {
            return null;
        }
        $assign = $node->expr;
        $var = $assign->var;
        $func_call = $assign->expr;
        if (! $func_call instanceof FuncCall) {
            return null;
        }
        if (! $this->isName($func_call->name, 'mysqli_query')) {
            return null;
        }
        if (count($func_call->args) < 2) {
            return null;
        }
        $query = $func_call->args[1]->value;
        // deze regel behandelt het geval met de query in een string-met-onderbrekingen
        if (! $this->looksLikeString($query)) {
            return null;
        }
        [$sql, $args, $func_args] = $this->splitArgsFromString($query);
        $gateway = 'TODO';
        return [
            # $this->dit_werkte($sql, $args),
            $this->composeDeclaration($gateway),
            $this->composeCall($assign, $gateway, $func_args),
            $this->composeMethod($assign, $func_args, $sql, $args),
        ];
    }

}
