@extends('layout/app')

@section('content')

@isset($error)
<div class="row text-center">
    <div class="col-12 col-md-4 offset-md-4">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <p>{{$error}}</p>
            </div>
        </div>
    </div>
</div>
@endisset

{{-- <div class="row my-5 text-center">
    <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
        <h4 class="mb-3">hãy điền tên hoặc username tài khoản Facebook của bạn để tìm kiếm nhé</h4>

        <div style="max-width:400px" class="mx-auto">
            <form action="/result" method="get">
                <input name ="query" type="text" class="form-control my-3" placeholder="Search..." autocomplete="off" value='{{$query ?? ""}}' />
                <button type="submit" class="btn btn-dark btn-rounded width-md waves-effect waves-light" href="/result"><strong>Kiểm tra ngay</strong></button>
            </form>
        </div>
        
    </div>
</div> --}}

@isset($queryResult)

    @if ($queryResult->total())
        <div class="row my-5">
            <div class="col-12 col-lg-8 offset-lg-2">
                <h5 class="mb-2 text-blue">Có {{$queryResult->total()}} kết quả được tìm thấy với "{{$query}}"</h5>

                <ul class="list-group mb-0 user-list">
                    @foreach ($queryResult->items() as $item)
                        <li class="list-group-item my-1">
                            <a href="/result/{{$item->id}}" class="user-list-item">
                                <div class="user avatar-sm float-left mr-2">
                                    <img src="data:image/png;base64,{{$item->avatar}}" alt="Ảnh cá nhân của {{$item->name}}" class="img-fluid rounded-circle">
                                </div>
                                <div class="user-desc">
                                    <h5 class="name mt-0 mb-1">{{$item->name}}</h5>
                                    @if(strpos($item->fburl, "?") === false)
                                        <span class="desc text-muted mb-0 font-12"> {{str_replace("/", "@", $item->fburl)}}</span>
                                    @endif
                                </div>
                            </a>
                        </li>
                                    
                    @endforeach

                </ul>
                {{ $queryResult->appends(['query' => $query])->links() }}
                
            </div>
        </div>
    @else
    <div class="row my-5">
        <div class="col-12 col-lg-8 offset-lg-2">
            <h5 class="mb-2 text-blue">Không tìm thấy kết quả nào với "{{$query}}"</h5>
            
            {{-- <div class="card-box">
                <h4 class="header-title mt-0 mb-4">Để hệ thống có thể nhận ra sự tham gia của bạn, hãy đảm bảo bạn đã</h4>

                <div class="mt-0">1. Thay ảnh cá nhân với Frame đã cung cấp, có đủ hashtag <span class="text-blue">#mungdoan89tuoi 
                    #avatarthanhnienchongdich</span></div>
                <div class="progress progress-bar-alt-primary progress-sm mt-0 mb-3">
                    <div class="progress-bar bg-primary progress-animated wow animated animated" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>

                <div class="mt-0">2. Like hoặc bày tỏ cảm xúc với <a href="https://www.facebook.com/3077368202316123" target="_blank">bài viết này</a> </div>
                <div class="progress progress-bar-alt-primary progress-sm mt-0 mb-3">
                    <div class="progress-bar bg-primary progress-animated wow animated animated" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>

                <div class="mt-0 text-success">Nếu bạn đã thực hiện 2 thao tác trên, hãy yên tâm, hệ thống sẽ tìm ra phần dự thi của bạn trong ít phút nữa thôi. </div>
                <div class="progress progress-bar-alt-primary progress-sm mt-0 mb-3">
                    <div class="progress-bar bg-primary progress-animated wow animated animated" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>


            </div> --}}
        </div>
    </div>
    @endif
@endisset

@endsection

@section('script')
    <script>
    </script>
@endsection



