<html>
<head>
    <meta charset="utf-8">
    <meta name=ProgId content=Excel.Sheet>
    <meta name=Generator content="Microsoft Excel 14">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        <!--
        table {
            mso-displayed-decimal-separator: "\.";
            mso-displayed-thousand-separator: "\,";
        }

        @page {
            margin: 1.0in .75in 1.0in .75in;
            mso-header-margin: .5in;
            mso-footer-margin: .5in;
        }

        .style0 {
            mso-number-format: General;
            text-align: general;
            vertical-align: bottom;
            white-space: nowrap;
            mso-rotate: 0;
            mso-background-source: auto;
            mso-pattern: auto;
            color: black;
            font-size: 12.0pt;
            font-weight: 400;
            font-style: normal;
            text-decoration: none;
            font-family: Calibri, sans-serif;
            mso-font-charset: 134;
            border: none;
            mso-protection: locked visible;
            mso-style-name: Normal;
            mso-style-id: 0;
        }

        td {
            mso-style-parent: style0;
            padding-top: 1px;
            padding-right: 1px;
            padding-left: 1px;
            mso-ignore: padding;
            color: black;
            font-size: 12.0pt;
            font-weight: 400;
            font-style: normal;
            text-decoration: none;
            font-family: Calibri, sans-serif;
            mso-font-charset: 134;
            mso-number-format: General;
            text-align: general;
            vertical-align: bottom;
            border: 1px solid;
            mso-background-source: auto;
            mso-pattern: auto;
            mso-protection: locked visible;
            white-space: nowrap;
            mso-rotate: 0;
        }

        .text {
            mso-style-parent: style0;
            mso-number-format: "\@";
        }

        -->
    </style>
</head>
<body link=blue vlink=purple>

<block name="dataExportContent">
    <table border=0 cellpadding=0 cellspacing=0 style="border-collapse: collapse; table-layout: fixed">
        <tr height="60">
            <td colspan="<?php echo count($listFields) + 1 ?>" style="text-align:center; vertical-align:middle; ">
                <span style="font-size:26px;">{$subject}</span>
            </td>
        </tr>
        <?php if($customHeader){ ?>
        {$customHeader}
        <?php }else{ ?>
        <tr height=15 style='height: 15.0pt'>
            <td style="text-align:center">序号</td>
            <?php foreach($listFields as $key=>$vo){ ?>
            <td style="text-align:center">{$vo}</td>
            <?php } ?>
        </tr>
        <?php } ?>

        <?php foreach($objectData as $key=>$vo){ ?>
        <tr>
            <td class="text" style="text-align:center">{$key+1}</td>
            <?php foreach($listFields as $ke=>$fld){ ?>
            <td class="text" style="text-align: center">{$vo[$ke]|format_tpl_value=$fld['type']}</td>
            <?php } ?>
        </tr>
        <?php } ?>
    </table>
</block>

</body>
</html>
