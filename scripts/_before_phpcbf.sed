s/}/\n}\n/g
s/{/\n{\n/g
s/?>\s*/\n?>\n/g
s/php echo/=/g
s/\s*<?php/\n<?php\n/g
s/\s+$//g
/^$/d
${/^?>$/d;}
