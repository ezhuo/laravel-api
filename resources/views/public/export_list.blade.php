@extends('base.export')

@section('content')
    <table border=0 cellpadding=0 cellspacing=0 style="border-collapse: collapse; table-layout: fixed">

        <tr height="60">
            <td colspan="{{count($field) + 1}}" style="text-align:center; vertical-align:middle; ">
                <span style="font-size:26px;">{{$title}}</span>
            </td>
        </tr>
        <tr height=15 style='height: 15.0pt'>
            <td style="text-align:center">
                序号
            </td>
            @foreach ($field as $fl=>$column)
                <td style="text-align:center;width: {{$column['width']}}px;">
                    {{$column['title']}}
                </td>
            @endforeach
        </tr>
        @foreach ($data as $key=>$row)
            <tr>
                <td class="text" style="text-align:center">
                    {{$key+1}}
                </td>
                @foreach ($field as $fl=>$column)
                    <td class="text" style="text-align: center">
                        @if (isset($column['dict_type']) && isset($dict[$column['dict_type']][$row[$fl]]))
                            {{$dict[$column['dict_type']][$row[$fl]]}}
                        @else
                            {{$row[$fl]}}
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach


    </table>
@stop