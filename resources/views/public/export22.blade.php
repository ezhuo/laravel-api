@extends('base.base')

@section('content')
    <table border=0 cellpadding=0 cellspacing=0 style="border-collapse: collapse; table-layout: fixed">

        <tr height="60">
            <td colspan="<?php echo count($listFields) + 1 ?>" style="text-align:center; vertical-align:middle; ">
                <span style="font-size:26px;">{{$title}}</span>
            </td>
        </tr>

        <tr height=15 style='height: 15.0pt'>
            <td style="text-align:center">
                序号
            </td>
            <?php foreach($listFields as $field=>$field_title){ ?>
            <td style="text-align:center">
                {{$field_title}}
            </td>
            <?php } ?>
        </tr>

        <?php foreach($objectData as $key=>$row){ ?>
        <tr>
            <td class="text" style="text-align:center">
                {{$key+1}}
            </td>
            <?php foreach($listFields as $field=>$field_title){ ?>
            <td class="text" style="text-align: center">
                {{$row[$field]}}
            </td>
            <?php } ?>
        </tr>
        <?php } ?>

    </table>
@stop