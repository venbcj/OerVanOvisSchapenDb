<?php

namespace Utils\Rector\Rector;

use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;

trait ArgSplitter {
    
    private function splitArgsFromString($expr) {
        $parts = $this->flattenConcat($expr);
        $sql = '';
        $args = [];
        $i = 1;
        foreach ($parts as $part) {
            if ($part instanceof String_) {
                $sql .= $part->value;
            } else {
                $param = ':param' . $i++;
                $sql .= $param;
                $variable = $this->unwrapEscapeCall($part);
                $arg = [$param, $variable];
                if ($this->canInferTypeFromName($variable)) {
                    $arg[] = $this->inferTypeFrom($variable);
                }
                $args[] = $arg;
            }
        }
        return [$sql, $args];
    }

    // TODO: heuristiekjes
    // *Id -> int
    // *at -> int ("aantal")
    // dm* -> date
    // *dm -> date
    private function canInferTypeFromName(Node $variable): bool {
        return false;
    }

    // TODO: zoiets als self::INT
    private function inferTypeFrom(Node $variable): Node {
    }

    private function flattenConcat(Concat $concat): array {
        return array_merge(
            $concat->left instanceof Concat ? $this->flattenConcat($concat->left) : [$concat->left],
            $concat->right instanceof Concat ? $this->flattenConcat($concat->right) : [$concat->right]
        );
    }

    private function unwrapEscapeCall(Node $expr): Node {
        if (
            $expr instanceof Node\Expr\FuncCall
            && $expr->name instanceof Node\Name
            && $expr->name->toString() === 'mysqli_real_escape_string'
            && isset($expr->args[1])
        ) {
            return $expr->args[1]->value; // de variabele die ge-escaped werd
        }
        return $expr;
    }

}
