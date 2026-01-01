<?php

declare(strict_types=1);

namespace Utils\Rector\Rector;

use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Arg;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Rector\NodeTypeResolver\Node\AttributeKey;

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
        return [FuncCall::class];
    }

    public function refactor(Node $node) {
        if (! $node instanceof FuncCall) {
            return null;
        }
        if (! $this->isName($node->name, 'mysqli_query')) {
            return null;
        }
        if (count($node->args) < 2) {
            return null;
        }
        // deze regel behandelt het geval met de query in een string-met-onderbrekingen
        if (! $node->args[1]->value instanceof Concat) {
            return null;
        }
        # throw new \Exception(get_class($node->args[1]->value));
        [$sql, $args] = $this->splitArgsFromString($node->args[1]->value);
        return new MethodCall(
            new Variable('this'),
            'run_query',
            [
                new Arg(new String_($sql, [
                    AttributeKey::KIND => String_::KIND_HEREDOC,
                    AttributeKey::DOC_LABEL => 'SQL',
                ])),
                new Arg($this->nodeFactory->createArray($args))
            ]
        );
    }

}
