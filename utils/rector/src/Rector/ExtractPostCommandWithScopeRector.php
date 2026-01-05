<?php

namespace Utils\Rector\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Name;
use PhpParser\PrettyPrinter\Standard;
use Rector\Rector\AbstractRector;
use Rector\NodeTypeResolver\Node\AttributeKey;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

final class ExtractPostCommandWithScopeRector extends AbstractRector
{

    private const STATE = '__pagestate';

    public function getNodeTypes(): array
    {
        return [If_::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (!$this->isPostIssetIf($node)) {
            return null;
        }

        $handlerName = $this->resolveHandlerName($node);
        $assignedVars = $this->collectAssignedVariables($node->stmts);
        $handler = $this->createHandlerFunction(
            $handlerName,
            $node->stmts,
            $assignedVars
        );

        $call = new Expression(
            new Assign(
                new Variable(self::STATE),
                new FuncCall(
                    new Name($handlerName),
                    [
                        new Arg(new Variable('_POST')),
                        new Arg(
                            new Coalesce(
                                new Variable(self::STATE),
                                new Array_()
                            )
                        ),
                    ]
                )
            )
        );

        $compact = $this->scopeCompactor();

        $extract = new Expression(new FuncCall(
            new Name('extract'), [
                new Arg(new Variable(self::STATE)),
            ]));

        // vervang body van if door handler call
        $node->stmts = [
            $handler,
            $compact,
            $call,
            $extract,
        ];

        return $node;
    }

    // ------------------------------------------------------------

    private function scopeCompactor() {
        $foreachNode = new Foreach_(
            new FuncCall(new Name('get_defined_vars')),  // get_defined_vars()
            new Variable('varName'),
            [
                'valueVar' => new Variable('value'),
                'stmts' => [
                    new Expression(
                        new FuncCall(
                            new Name('array_key_exists'), // pseudo-if via rector node: we vervangen dit door een if later
                            [] // wordt via If_ node opgebouwd, zie hieronder
                        )
                    )
                ]
            ]
        );
        $ifNode = new \PhpParser\Node\Stmt\If_(
            new \PhpParser\Node\Expr\BooleanNot(
                new FuncCall(
                    new Name('in_array'),
                    [
                        new Arg(new Variable('varName')),
                        new Arg(new Array_([
                            new ArrayItem(new String_('_GET')),
                            new ArrayItem(new String_('_POST')),
                            new ArrayItem(new String_('_COOKIE')),
                            new ArrayItem(new String_('_SESSION')),
                            new ArrayItem(new String_('_FILES')),
                            new ArrayItem(new String_('_SERVER')),
                            new ArrayItem(new String_('_ENV')),
                            new ArrayItem(new String_('GLOBALS')),
                            new ArrayItem(new String_('__pageState')),
                            new ArrayItem(new String_('input')),
                        ]))
                    ]
                )
            ),
            [
                'stmts' => [
                    new Expression(
                        new Assign(
                            new ArrayDimFetch(new Variable('__pageState'), new Variable('varName')),
                            new Variable('value')
                        )
                    )
                ]
            ]
        );
        $foreachNode->stmts = [$ifNode];
        return $foreachNode;
    }

    private function isPostIssetIf(If_ $if): bool
    {
        $cond = $if->cond;

        if (!$cond instanceof Expr\Isset_) {
            return false;
        }

        foreach ($cond->vars as $var) {
            if ($var instanceof ArrayDimFetch
                && $var->var instanceof Variable
                && $var->var->name === '_POST'
            ) {
                return true;
            }
        }

        return false;
    }

    private function resolveHandlerName(If_ $if): string
    {
        // stabiele maar unieke naam
        return 'handle_post_' . $if->getStartLine();
    }

    private function collectAssignedVariables(array $stmts): array
    {
        $vars = [];

        $stack = $stmts;
        while ($stack) {
            $stmt = array_pop($stack);

            if ($stmt instanceof Expression && $stmt->expr instanceof Assign) {
                if ($stmt->expr->var instanceof Variable) {
                    $vars[$stmt->expr->var->name] = true;
                }
            }

            foreach ($stmt->getSubNodeNames() as $sub) {
                $child = $stmt->$sub;
                if (is_array($child)) {
                    foreach ($child as $c) {
                        if ($c instanceof Node) {
                            $stack[] = $c;
                        }
                    }
                } elseif ($child instanceof Node) {
                    $stack[] = $child;
                }
            }
        }

        return array_keys($vars);
    }

    private function createHandlerFunction(
        string $name,
        array $stmts,
        array $assignedVars
    ): Function_ {

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new class extends NodeVisitorAbstract {
            public function enterNode(Node $node) {
                if ($node instanceof Expr\Variable && $node->name === '_POST') {
                    $node->name = 'input';
                }
            }
        });
        $stmts = $traverser->traverse($stmts);

        $extract = new Expression(new FuncCall(
            new Name('extract'), [
                new Arg(new Variable(self::STATE)),
            ]));
        $body =  $stmts;
        array_unshift($body, $extract);

        foreach ($assignedVars as $var) {
            if ($var === self::STATE) {
                continue;
            }

            $body[] = new Expression(
                new Assign(
                    new ArrayDimFetch(
                        new Variable(self::STATE),
                        new String_($var)
                    ),
                    new Variable($var)
                )
            );
        }

        $body[] = new Return_(new Variable(self::STATE));

        $function = new Function_(
            $name,
            [
                'params' => [
                    new Node\Param(new Variable('input'), null, new Name('array')),
                    new Node\Param(new Variable(self::STATE), null, new Name('array')),
                ],
                'stmts' => $body,
                'returnType' => new Name('array'),
            ],
        );
        $function->namespacedName = new Name($name);
        return $function;
    }
}
