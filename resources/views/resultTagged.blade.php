@empty($players)
<tr>
    <td colspan="7">
        Không có ai trong danh sách
    </td>
</tr>
@endempty
@foreach ($players as $tagged)
<tr data-status = "{{$tagged['status']}}">
    <td><a class="co-name text-info" href="https://www.facebook.com/{{$tagged['comment']}}" target="_blank" data-toggle="tooltip" data-placement="bottom"  title="Được tag bởi {{ $tagged['taggedBy'] }}">{{  $tagged['name']   }} </a></td>
        @if($tagged['status'] == "success")
            <td class="text-center">
                <h4 class="font-weight-normal text-primary">{{ $tagged['like'] }}</h4>
            </td>
            <td class="text-center">
                <h4 class="font-weight-normal text-pink">{{ $tagged['love'] }}</h4>
            </td>
            <td class="text-center">
                <h4 class="font-weight-normal text-warning">{{ $tagged['haha'] }}</h4>
            </td>
            <td class="text-center">
                <h4 class="font-weight-normal text-warning">{{ $tagged['wow'] }}</h4>
            </td>
            <td class="text-center">
                <h4 class="font-weight-normal text-warning">{{ $tagged['sad'] }}</h4>
            </td>
            <td class="text-center">
                <h4 class="font-weight-normal text-danger">{{ $tagged['angry'] }}</h4>
            </td>
            <td class="text-center">
                <h4 class="font-weight-normal text-white">{{ $tagged['point'] }}</h4>
            </td>
        @elseif($tagged['status'] == "not-first-tag")
            <td colspan="7">
                Hệ thống chỉ tính điểm cho người người bạn tag lần đầu
            </td>
        @elseif($tagged['status'] == "ineligible-tag")
            <td colspan="7">
                Lượt tag này không hợp lệ. Chỉ chấp nhận tag tối đa 3 người
            </td>
        @elseif($tagged['status'] == "ineligible-person")
            <td colspan="7">
                Người này đã vi phạm luật chơi.
            </td>
        @elseif($tagged['status'] == "unlike-post")
            <td colspan="7">
                Người này chưa like <a href="https://www.facebook.com/3077368202316123" target="_blank"> bài viết giới thiệu chương trình </a>
            </td>
        @elseif($tagged['status'] == "not-have-hastag")
            <td colspan="7">
                Hãy nhắc người này đặt hashtag trong ảnh đại diện nhé
            </td>
        @elseif($tagged['status'] == "repeated")
            <td class="text-left" colspan="7">
                Điểm người này đã được tính cho bạn rồi á
            </td>
        @else
            <td colspan="4">
                error
            </td>
    @endif
@endforeach
</tr>