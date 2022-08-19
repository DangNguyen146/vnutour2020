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

@isset($post)
    <div class="row mb-5">
        <div class="col">
            <div class="d-flex justify-content-center">
                <div class="card" style="min-width: 40%">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row mx-auto px-1 justify-content-between" >
                            <div class="wid-u-info mx-3 mt-3">
                                <h3 class="mt-0"><small>Team</small> 
                                   <a class="text-white" href="https://www.facebook.com/{{ $post->post_id }}" target="_blank" title="Click để xem bài trên FB">
                                    {{$post->title}}
                                   </a>    
                               </h3>
                               @if(strpos($post->fburl, "?") === false)
                                   <p class="desc text-muted mt-0 mb-2 font-12"> {{str_replace("/", "@", $post->fburl)}}</p>
                               @endif


                               @if($post->ranking)
                                   <p class="font-15 text-truncate text-warning">Xếp hạng: <strong>#{{$post->ranking}}</strong></p>
                               @else
                               <p class="font-13 text-truncate text-danger">Chưa đủ điều kiện để xếp hạng</strong></p>
                               @endif
                           </div>
                            {{-- <div class="avatar-xl">
                                <img src="data:image/png;base64,{{$post->avatar}}" class="img-fluid rounded-circle" alt="user">
                            </div> --}}
                            @if ($post->type == "image")
                                <div>
                                    <img style="max-height: 400px;max-width:400px" class="img-fluid" src="{{ $post->image }}" alt="">
                                </div>
                            @elseif ($post->type == "video")
                                <script async defer src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2"></script>
                                <div class="fb-video" 
                                data-href="https://www.facebook.com/watch/?v={{ $post->image }}" 
                                data-width="1440"
                                data-allowfullscreen="true"
                                data-autoplay="true"
                                data-show-captions="true"
                                data-lazy="true">
                            </div>
                            {{-- <iframe src="https://www.facebook.com/plugins/video.php?height=314&href=https://www.facebook.com/watch/?v={{ $post->image }}" width="560" height="314" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media" allowFullScreen="true"></iframe> --}}
                            @else 
                                <div style="max-height: 400px;overflow: auto;width:calc(100%)">
                                    <img class="img-fluid" src="/assets/images/default_image.jpg" alt="">
                                </div>
                            @endif
                            
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($post->point)
        <div class="row mb-5">
            <div class="col">
                <div class="d-flex flex-column flex-lg-row">

                    <div class="mx-auto my-3 my-md-auto"></div>

                    <div class="d-flex justify-content-center">
                        @foreach ($reactionTypes as $reactionType)
                            <div class="d-flex widget-user mx-1 mx-md-3 pt-1">
                                <div class="text-center">
                                    <h2 class="font-weight-normal {{ $reactionType->css_class }} my-0 my-md-2" data-plugin="counterup">{{ $post[$reactionType->id] }}</h2>
                                    <img src="{{ $reactionType->image }}" width="40" class="img-fluid rounded-circle">
                                    <div>{{ $reactionType->getPoint() > 0 ?"+":"-" }}{{ $reactionType->getPoint() }}</div>
                                </div>
                            </div>
                        @endforeach

                        <div class="d-flex widget-user mx-3  pt-1">
                            <div class="text-center">
                                <h2 class="font-weight-normal text-primary  my-0 my-md-2" data-plugin="counterup">{{ $post->shared }}</h2>
                                <img src="/assets/images/reaction/share.png" width="40" class="img-fluid rounded-circle">
                                <div>+3</div>
                            </div>
                        </div>
                    </div>

                    <div class="mx-auto my-3 my-md-auto"></div>

                    <div class="d-flex justify-content-center">

                        <div class="text-center mx-3" data-toggle="tooltip" title="Điểm nhận được khi bài dự thi được bạn bè like và thả cảm xúc">
                            <h1 class="font-weight-normal text-pink" data-plugin="counterup">{{ $post->caclReactionPoint() }}</h1>
                            <h5>cảm xúc</h5>
                        </div>
                        
                        <div class="d-flex widget-user pt-3">
                            <div class="text-center">
                                <h2 class="font-weight-normal">+</h2>
                            </div>
                        </div>

                        <div class="text-center mx-4" data-toggle="tooltip" title="Điểm nhận được khi bài dự thi được chia sẻ">
                            <h1 class="font-weight-normal text-success" data-plugin="counterup">{{ $post->shared * 3 }}</h1>
                            <h5>chia sẻ</h5>
                        </div>

                        <div class="d-flex widget-user pt-3">
                            <div class="text-center">
                                <h2 class="font-weight-normal">=</h2>
                            </div>
                        </div>

                        <div class="text-center mx-3">
                            <h1 class="font-weight-normal text-info" data-plugin="counterup">{{ $post->point }}</h1>
                            <h5>điểm</h5>
                        </div>   
                    </div>

                    <div class="mx-auto my-3 my-md-auto"></div>
                </div>
            </div><!-- end col -->
        </div>
    @endif

    {{-- @foreach ($reactionsByCategories as $type => $reactionsByCategory) --}}
    @foreach ($reactionTypes as $reactionType)
    @php
        $type = $reactionType->id;
        if(array_key_exists($type, $reactionsByCategories)){
            $reactionsByCategory = $reactionsByCategories[$type];
        }else{
            continue;
        }
    @endphp
        <div class="row mt-5">
            <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
                <div class="card card-body">
                    <p class="card-text">
                        <div class="responsive-table-plugin">
                            <div class="table-rep-plugin">
                                <div class="table-responsive" data-pattern="priority-columns">
                                    <table id="tech-companies-1" class="table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <h4 class="card-title {{ $reactionsByCategory['meta']->css_class }} float-left" data-toggle="tooltip" title="Bạn bè đã bộc lộ cảm xúc '{{ $reactionsByCategory['meta']->title }}'">
                                                        <img src="{{ $reactionsByCategory['meta']->image }}" width="40" class="img-fluid rounded-circle">
                                                        <small class="pl-2 font-weight-bold">{{ count($reactionsByCategory['data']['eligible']) }} lượt {{ $reactionsByCategory['meta']->id }}</small>
                                                        @if (count($reactionsByCategory['data']['not_like']))
                                                            <small class="pl-2 text-mute"> (+{{ count($reactionsByCategory['data']['not_like']) }} lượt không được tính điểm)</small>
                                                        @endif
                                                    </h4>
                                                    <div class="float-right">
                                                         <span class="text-danger">{{ count($reactionsByCategory['data']['eligible']) * $reactionsByCategory['meta']->getPoint() }} điểm</span>
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    @foreach ($reactionsByCategory['data']['eligible'] as $reaction)
                                                        <a class="co-name text-success" href="https://www.facebook.com/{{ $reaction->fbuser->fburl }}" target="_blank" data-toggle="tooltip" data-placement="bottom"  title="">{{ $reaction->fbuser->name }}</a>, 
                                                    @endforeach
                                                </td>
                                            </tr>
                                            @if (count($reactionsByCategory['data']['not_like']))
                                                <tr>
                                                    <td>
                                                        <h6 class="text-white">
                                                            Bạn hãy nhắc những bạn sau like  <a href="https://facebook.com/VNUTour"> Fanpage </a>để được tính điểm cho bạn nhé
                                                        </h6>
                                                        @foreach ($reactionsByCategory['data']['not_like'] as $reaction)
                                                            <a class="co-name text-info" href="https://www.facebook.com/{{ $reaction->fbuser->fburl }}" target="_blank" data-toggle="tooltip" data-placement="bottom"  title="">{{ $reaction->fbuser->name }}</a>, 
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            @endif
                                            @if (count($reactionsByCategory['data']['ineligible']))
                                            <tr  class="bg-secondary">
                                                <td>
                                                    <h5 class="text-white">
                                                        Nghi ngờ gian lận với các tài khoản
                                                    </h5>
                                                    @foreach ($reactionsByCategory['data']['ineligible'] as $reaction)
                                                        <a class="co-name text-info" href="https://www.facebook.com/{{ $reaction->fbuser->fburl }}" target="_blank" data-toggle="tooltip" data-placement="bottom"  title="">{{ $reaction->fbuser->name }} </a>, 
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </p>
                </div>
            </div>
        </div>
    @endforeach

    <div class="row mt-5">
        <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
            <div class="card card-body">
                <p class="card-text">
                    <div class="responsive-table-plugin">
                        <div class="table-rep-plugin">
                            <div class="table-responsive" data-pattern="priority-columns">
                                <table id="tech-companies-1" class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>
                                                <h4 class="card-title float-left" data-toggle="tooltip" title="Những người giúp lan toả bài dự thi của bạn">
                                                    <img src="/assets/images/reaction/share.png" width="40" class="img-fluid rounded-circle">
                                                    <small class="pl-2 font-weight-bold">{{ count($shares) }} lượt chia sẻ (chế độ công khai)</small>
                                                </h4>
                                                <div class="float-right">
                                                     <span class="text-danger">{{ count($shares) * 3 }} điểm</span>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                @foreach ($shares as $share)
                                                    <a class="co-name text-success" href="https://www.facebook.com/{{ $share->fbuser->fburl }}" target="_blank" data-toggle="tooltip" data-placement="bottom"  title="">{{ $share->fbuser->name }}</a>, 
                                                @endforeach
                                            </td>
                                        </tr>
                                        {{-- @if (count($reactionsByCategory['data']['not_like']))
                                            <tr>
                                                <td>
                                                    <h6 class="text-white">
                                                        Bạn hãy nhắc những bạn sau like  <a href="https://facebook.com/VNUTour"> Fanpage </a>để được tính điểm cho bạn nhé
                                                    </h6>
                                                    @foreach ($reactionsByCategory['data']['not_like'] as $reaction)
                                                        <a class="co-name text-info" href="https://www.facebook.com/{{ $reaction->fbuser->fburl }}" target="_blank" data-toggle="tooltip" data-placement="bottom"  title="">{{ $reaction->fbuser->name }}</a>, 
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endif
                                        @if (count($reactionsByCategory['data']['ineligible']))
                                        <tr  class="bg-secondary">
                                            <td>
                                                <h5 class="text-white">
                                                    Nghi ngờ gian lận với các tài khoản
                                                </h5>
                                                @foreach ($reactionsByCategory['data']['ineligible'] as $reaction)
                                                    <a class="co-name text-info" href="https://www.facebook.com/{{ $reaction->fbuser->fburl }}" target="_blank" data-toggle="tooltip" data-placement="bottom"  title="">{{ $reaction->fbuser->name }} </a>, 
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endif --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </p>
            </div>
        </div>
    </div>

@endisset 

<script>
    let lastSync = {{ $post->updated_at->timestamp }}000;
</script>
@endsection


