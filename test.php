<?php

// plain string
// @KOMNIVOOR
$var = "SELECT FROM tblTable";
mysqli_query($db, $var);
// interpolated string
$delete = "DELETE FROM tblBla values($ram)";
mysqli_query($db, $delete);
// concatenated string
$insert = "INSERT INTO tblBla values(".mysqli_real_escape_string($db, $berg).")";
mysqli_query($db, $insert);
// inline concatenated string
// @KOMNIVOOR
mysqli_query($db, "UPDATE users SET name='".mysqli_real_escape_string($db, $name)."'");
// inline string with result
$res = mysqli_query($db, "SELECT $fields FROM $table WHERE value='".mysqli_real_escape_string($db, $ram)."' ORDER BY worst $limit");
/*

====> File test.php:
==> Node dump:
array(
    0: Stmt_Expression(
        expr: Expr_Assign(
            var: Expr_Variable(
                name: var
            )
            expr: Scalar_String(
                value: SELECT FROM table
            )
        )
        comments: array(
            0: // plain string
        )
    )
    1: Stmt_Expression(
        expr: Expr_FuncCall(
            name: Name(
                name: mysqli_query
            )
            args: array(
                0: Arg(
                    name: null
                    value: Expr_Variable(
                        name: db
                    )
                    byRef: false
                    unpack: false
                )
                1: Arg(
                    name: null
                    value: Expr_Variable(
                        name: var
                    )
                    byRef: false
                    unpack: false
                )
            )
        )
    )
    2: Stmt_Expression(
        expr: Expr_Assign(
            var: Expr_Variable(
                name: delete
            )
            expr: Scalar_InterpolatedString(
                parts: array(
                    0: InterpolatedStringPart(
                        value: DELETE INTO bla values(
                    )
                    1: Expr_Variable(
                        name: ram
                    )
                    2: InterpolatedStringPart(
                        value: )
                    )
                )
            )
        )
        comments: array(
            0: // interpolated string
        )
    )
    3: Stmt_Expression(
        expr: Expr_FuncCall(
            name: Name(
                name: mysqli_query
            )
            args: array(
                0: Arg(
                    name: null
                    value: Expr_Variable(
                        name: db
                    )
                    byRef: false
                    unpack: false
                )
                1: Arg(
                    name: null
                    value: Expr_Variable(
                        name: delete
                    )
                    byRef: false
                    unpack: false
                )
            )
        )
    )
    4: Stmt_Expression(
        expr: Expr_Assign(
            var: Expr_Variable(
                name: insert
            )
            expr: Expr_BinaryOp_Concat(
                left: Expr_BinaryOp_Concat(
                    left: Scalar_String(
                        value: INSERT INTO bla values(
                    )
                    right: Expr_FuncCall(
                        name: Name(
                            name: mysqli_real_escape_string
                        )
                        args: array(
                            0: Arg(
                                name: null
                                value: Expr_Variable(
                                    name: db
                                )
                                byRef: false
                                unpack: false
                            )
                            1: Arg(
                                name: null
                                value: Expr_Variable(
                                    name: berg
                                )
                                byRef: false
                                unpack: false
                            )
                        )
                    )
                )
                right: Scalar_String(
                    value: )
                )
            )
        )
        comments: array(
            0: // concatenated string
        )
    )
    5: Stmt_Expression(
        expr: Expr_FuncCall(
            name: Name(
                name: mysqli_query
            )
            args: array(
                0: Arg(
                    name: null
                    value: Expr_Variable(
                        name: db
                    )
                    byRef: false
                    unpack: false
                )
                1: Arg(
                    name: null
                    value: Expr_Variable(
                        name: insert
                    )
                    byRef: false
                    unpack: false
                )
            )
        )
    )
    6: Stmt_Expression(
        expr: Expr_Assign(
            var: Expr_Variable(
                name: res
            )
            expr: Expr_FuncCall(
                name: Name(
                    name: mysqli_query
                )
                args: array(
                    0: Arg(
                        name: null
                        value: Expr_Variable(
                            name: db
                        )
                        byRef: false
                        unpack: false
                    )
                    1: Arg(
                        name: null
                        value: Expr_BinaryOp_Concat(
                            left: Expr_BinaryOp_Concat(
                                left: Scalar_InterpolatedString(
                                    parts: array(
                                        0: InterpolatedStringPart(
                                            value: SELECT 
                                        )
                                        1: Expr_Variable(
                                            name: fields
                                        )
                                        2: InterpolatedStringPart(
                                            value:  FROM 
                                        )
                                        3: Expr_Variable(
                                            name: table
                                        )
                                        4: InterpolatedStringPart(
                                            value:  WHERE value='
                                        )
                                    )
                                )
                                right: Expr_FuncCall(
                                    name: Name(
                                        name: mysqli_real_escape_string
                                    )
                                    args: array(
                                        0: Arg(
                                            name: null
                                            value: Expr_Variable(
                                                name: db
                                            )
                                            byRef: false
                                            unpack: false
                                        )
                                        1: Arg(
                                            name: null
                                            value: Expr_Variable(
                                                name: ram
                                            )
                                            byRef: false
                                            unpack: false
                                        )
                                    )
                                )
                            )
                            right: Scalar_InterpolatedString(
                                parts: array(
                                    0: InterpolatedStringPart(
                                        value: ' ORDER BY worst 
                                    )
                                    1: Expr_Variable(
                                        name: limit
                                    )
                                )
                            )
                        )
                        byRef: false
                        unpack: false
                    )
                )
            )
        )
    )
    7: Stmt_Nop(
        comments: array(
            0: /*
            
            ====> File test.php:
            ==> Node dump:
            array(
               0: Stmt_Expression(
                   expr: Expr_Assign(
                       var: Expr_Variable(
                           name: delete
                       )
                       expr: Scalar_InterpolatedString(
                           parts: array(
                               0: InterpolatedStringPart(
                                   value: DELETE INTO bla values(
                               )
                               1: Expr_Variable(
                                   name: ram
                               )
                               2: InterpolatedStringPart(
                                   value: )
                               )
                           )
                       )
                   )
               )
               1: Stmt_Expression(
                   expr: Expr_FuncCall(
                       name: Name(
                           name: mysqli_query
                       )
                       args: array(
                           0: Arg(
                               name: null
                               value: Expr_Variable(
                                   name: db
                               )
                               byRef: false
                               unpack: false
                           )
                           1: Arg(
                               name: null
                               value: Expr_Variable(
                                   name: delete
                               )
                               byRef: false
                               unpack: false
                           )
                       )
                   )
               )
               2: Stmt_Expression(
                   expr: Expr_Assign(
                       var: Expr_Variable(
                           name: insert
                       )
                       expr: Expr_BinaryOp_Concat(
                           left: Expr_BinaryOp_Concat(
                               left: Scalar_String(
                                   value: INSERT INTO bla values(
                               )
                               right: Expr_FuncCall(
                                   name: Name(
                                       name: mysqli_real_escape_string
                                   )
                                   args: array(
                                       0: Arg(
                                           name: null
                                           value: Expr_Variable(
                                               name: db
                                           )
                                           byRef: false
                                           unpack: false
                                       )
                                       1: Arg(
                                           name: null
                                           value: Expr_Variable(
                                               name: berg
                                           )
                                           byRef: false
                                           unpack: false
                                       )
                                   )
                               )
                           )
                           right: Scalar_String(
                               value: )
                           )
                       )
                   )
               )
               3: Stmt_Expression(
                   expr: Expr_FuncCall(
                       name: Name(
                           name: mysqli_query
                       )
                       args: array(
                           0: Arg(
                               name: null
                               value: Expr_Variable(
                                   name: db
                               )
                               byRef: false
                               unpack: false
                           )
                           1: Arg(
                               name: null
                               value: Expr_Variable(
                                   name: insert
                               )
                               byRef: false
                               unpack: false
                           )
                       )
                   )
               )
               4: Stmt_Expression(
                   expr: Expr_Assign(
                       var: Expr_Variable(
                           name: res
                       )
                       expr: Expr_FuncCall(
                           name: Name(
                               name: mysqli_query
                           )
                           args: array(
                               0: Arg(
                                   name: null
                                   value: Expr_Variable(
                                       name: db
                                   )
                                   byRef: false
                                   unpack: false
                               )
                               1: Arg(
                                   name: null
                                   value: Expr_BinaryOp_Concat(
                                       left: Expr_BinaryOp_Concat(
                                           left: Scalar_InterpolatedString(
                                               parts: array(
                                                   0: InterpolatedStringPart(
                                                       value: appel 
                                                   )
                                                   1: Expr_Variable(
                                                       name: met
                                                   )
                                                   2: InterpolatedStringPart(
                                                       value:  kaas 
                                                   )
                                                   3: Expr_Variable(
                                                       name: en
                                                   )
                                                   4: InterpolatedStringPart(
                                                       value:  '
                                                   )
                                               )
                                           )
                                           right: Expr_FuncCall(
                                               name: Name(
                                                   name: mysqli_real_escape_string
                                               )
                                               args: array(
                                                   0: Arg(
                                                       name: null
                                                       value: Expr_Variable(
                                                           name: db
                                                       )
                                                       byRef: false
                                                       unpack: false
                                                   )
                                                   1: Arg(
                                                       name: null
                                                       value: Expr_Variable(
                                                           name: ram
                                                       )
                                                       byRef: false
                                                       unpack: false
                                                   )
                                               )
                                           )
                                       )
                                       right: Scalar_InterpolatedString(
                                           parts: array(
                                               0: InterpolatedStringPart(
                                                   value: ' worst 
                                               )
                                               1: Expr_Variable(
                                                   name: einde
                                               )
                                           )
                                       )
                                   )
                                   byRef: false
                                   unpack: false
                               )
                           )
                       )
                   )
               )
            )
        )
    )
)
 */
