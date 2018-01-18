<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{$filename}}</title>
</head>
<style>
    @font-face {
        font-family: 'Droid';
        font-style: normal;
        font-weight: normal;
        src: url({{env('APP_URL')}}/fonts/DroidSansFallback.ttf) format('truetype');
    }

    table, table tr th, table tr td {
        border: 1px solid #000000;
    }

    table {
        width: 750px;;
        line-height: 30px;
        vertical-align: middle;
        border-collapse: collapse;
    }

    .table-top {
        border-top: none;
        margin-top: -1px;
    }

    .text-left {
        text-align: left;
        padding-left: 10px;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
        padding-right: 10px;
    }
</style>
<body>
<div>
    <div style="height: 70px;font-size: 30px;text-align: center">
        {{$title}}
    </div>

    <div style="height: 35px;">
        <span style="float: left">编号：{{$data['order']['order_no']}}</span>
        <span style="float: right">评估日期：{{$data['order']['end_at']}}</span>
    </div>

    <table>
        <tr>
            <td style="width: 120px;" class="text-right">
                申请人姓名
            </td>
            <td style="width: 280px;" class="text-left">
                {{$data['order']['older_name']}}
            </td>
            <td style="width: 120px;" class="text-right">
                身份证号码
            </td>
            <td class="text-left">
                {{$data['order']['older_idcard']}}
            </td>
        </tr>
        <tr>
            <td class="text-right">
                家庭住址
            </td>
            <td colspan="3" class="text-left">
                {{$data['order']['canton_addrs']}}
            </td>
        </tr>
        <tr>
            <td class="text-right">
                联系人姓名
            </td>
            <td class="text-left">
                {{$data['order']['linkman']}}
            </td>
            <td class="text-right">
                联系人电话
            </td>
            <td class="text-left">
                {{$data['order']['linkman_tel']}}
            </td>
        </tr>
    </table>

    <table class="table-top">
        <tr>
            <td class="text-right" rowspan="2" style="width: 120px;">
                一级指标分级
            </td>
            <td class="text-left" style="width: 350px;">
                {{$data['report']['result_class1']}}：{{$data['report']['result_level1']}}级
                （{{$data['report']['result_score1']}}分）
            </td>
            <td class="text-left">
                {{$data['report']['result_class2']}}：{{$data['report']['result_level2']}}级
                （{{$data['report']['result_score2']}}分）
            </td>

        </tr>
        <tr>
            <td class="text-left">
                {{$data['report']['result_class3']}}：{{$data['report']['result_level3']}}级
                （{{$data['report']['result_score3']}}分）
            </td>
            <td class="text-left">
                {{$data['report']['result_class4']}}：{{$data['report']['result_level4']}}级
                （{{$data['report']['result_score4']}}分）
            </td>
        </tr>
        <tr>
            <td class="text-right">
                生活能力等级
            </td>
            <td colspan="2" class="text-left">
                {{$data['report']['result_initial_name']}}
                （{{$data['report']['result_initial_level']}}级）
            </td>
        </tr>

    </table>

    <table class="table-top">
        <tr>
            <td colspan="3" class="text-center text-bold">
                加分条款：在生活能力等级同等条件下,按加分多少优先提供相关服务
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <table style="width: 748px;border-width: 0px;border-color: #fff" border="0">
                    @foreach ($data['report']['result_beijing_info'] as $idx=>$row)
                        @foreach ($row['items'] as $col_idx=>$col)
                            <tr>
                                @if ($col_idx < 1)
                                    <td class="text-right" style="width: 118px;" rowspan="{{sizeof($row['items'])}}">
                                        <div style="line-height:20px;"
                                             class="text-left">{{$row['class_name']}}</div>
                                    </td>
                                @endif
                                <td class="text-left" style="width: 350px;">
                                    {{$col['item_name']}}
                                </td>
                                <td class="text-center">
                                    {{$col['item_score']}}分
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="text-center text-bold">
                评估加分总计:
            </td>
            <td class="text-center">
                {{$data['report']['result_bejing_score']}}分
            </td>
        </tr>
    </table>

    <table class="table-top">
        <tr>
            <td style="width: 120px;" class="text-right">
                养老服务需求
            </td>
            <td class="text-left">
                @foreach ($data['report']['result_need_info'] as $idx=>$row)
                    <span>{{$row['class_name']}}，</span>
                @endforeach
            </td>
        </tr>
        <tr>
            <td class="text-right">
                评估结论
            </td>
            <td class="text-left">
                {{$data['report']['result_comment']}}
            </td>
        </tr>
        <tr>
            <td class="text-right">
                <span style="line-height:20px;">建议养老<br/>服务形式</span>
            </td>
            <td class="text-left">
                {{$data['report']['yanglao_service']}}
            </td>
        </tr>
        <tr>
            <td class="text-right">
                评估员
            </td>
            <td class="text-left">
                {{$data['report']['org_account_name1']}}、
                {{$data['report']['org_account_name2']}}
            </td>
        </tr>

        <tr>
            <td class="text-right">
                监督员
            </td>
            <td class="text-left">
                {{$data['report']['org_account_name']}}
            </td>
        </tr>


    </table>

    <table>
        <tr>
            <td style="padding:10px;line-height: 15px;">
                <p>
                    注：老年人生活能力初步等级划分标准
                </p>
                <p style="">
                    0 能力完好（生活能自理）：
                </p>
                <p>
                    &nbsp;&nbsp;&nbsp;日常生活活动、精神状态、感知觉与沟通分级均为 0，社会参与分级为 0 或 1。
                </p>
                <p>
                    1 轻度失能（需要介助服务）：
                </p>
                <p>
                    &nbsp;&nbsp; 日常生活活动分级为 0，但精神状态、感知觉与沟通中至少一项分级为 1 及以上，或社会参与 的分级为 2； 或日常生活活动分级为
                    1，精神状态、感知觉与沟通、社会参与中至少有一项的分级为 0 或 1。
                </p>
                <p>
                    2 中度失能（需要介助服务）：
                </p>
                <p>
                    &nbsp;&nbsp; 日常生活活动分级为 1，但精神状态、感知觉与沟通、社会参与均为 2，或有一项为 3； 或日常生活活动分级为 2，且精神状态、感知觉与沟通、社会参与中有 1-2 项的分级为 1
                    或 2。
                </p>
                <p>
                    3 重度失能（需要介护服务）：
                </p>
                <p>
                    &nbsp;&nbsp; 日常生活活动的分级为 3； 或日常生活活动、精神状态、感知觉与沟通、社会参与分级均为 2 或以上；
                    或日常生活活动分级为2，且精神状态、感知觉与沟通、社会参与中至少有一项分级为3
                </p>
            </td>
        </tr>
    </table>

</div>
</body>
</html>