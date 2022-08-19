@extends('layout/app')

@section('content')



@if (!$posts)
<div class="row">
    <div class="col top2">
    <div class="row mt-5 mb-3">
        <div class="col">
            <h6 class="mx-auto">
                Không có dữ liệu
            </h6>
        </div>
    </div>
</div>
@else
    <div class="row">
        <div class="col top2">
            

    @for ($i = 0; $i < count($posts); $i++)
    @if ($i == 0)
        <div class="row mt-5 mb-3">
            <div class="col">
                <h3 class="mx-auto">
                    TOP 5
                    <span class="float-right text-success">+20 điểm vào vòng loại</span>
                </h3>
            </div>
        </div>
    @elseif($i == 5)
        <div class="row mt-5 mb-3">
            <div class="col">
                <h3 class="mx-auto">
                    TOP 15
                    <span class="float-right text-success">+15 điểm vào vòng loại</span>
                </h3>
            </div>
        </div>
    @elseif($i == 15)
        <div class="row mt-5 mb-3">
            <div class="col">
                <h3 class="mx-auto">
                    TOP 30
                    <span class="float-right text-success">+10 điểm vào vòng loại</span>
                </h3>
            </div>
        </div>
    @elseif($i == 15)
        <div class="row mt-5 mb-3">
            <div class="col">
                <h3 class="mx-auto">
                    ngoài top 30
                    <span class="float-right text-success">+10 điểm vào vòng loại khi tổng điểm >= 120</span>
                </h3>
            </div>
        </div>
    @endif
            <a href="/result/{{$posts[$i]->id}}">
                <div class="card-box">
                    <div class="d-flex flex-column flex-md-row">

                        <div class="d-flex justify-content-center rank-title-wrapper">
                            <div>
                                <h2 class="text-white font-weight-normal">#{{$posts[$i]->ranking}}</h2>
                                <h3 class="text-danger font-weight-normal">{{$posts[$i]->title}}</h3>
                            </div>
                            <div class="mx-auto"></div>
                            <div class="d-flex flex-column align-items-center mr-4">
                               
                            </div>
                            <div class="mx-auto mx-md-0"></div>
                        </div>

                        <div class="mx-auto"></div>

                        <div class="mx-auto mx-md-0 my-3 my-md-auto"></div>

                        <div class="d-flex d-lg-flex d-md-none justify-content-center">
                            @foreach ($reactionTypes as $reactionType)
                                <div class="d-flex widget-user mx-1 mx-sm-3  pt-1">
                                    <div class="text-center">
                                        <h2 class="font-weight-normal {{ $reactionType->css_class }}  my-0 my-md-2" data-plugin="counterup">{{ $posts[$i][$reactionType->id] }}</h2>
                                        <img src="{{ $reactionType->image }}" width="35" class="img-fluid rounded-circle">
                                        <div>{{ $reactionType->getPoint() > 0 ?"+":"-" }}{{ $reactionType->getPoint() }}</div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="d-flex widget-user mx-3  pt-1">
                                <div class="text-center">
                                    <h2 class="font-weight-normal text-primary  my-0 my-md-2" data-plugin="counterup">{{ $posts[$i]->shared }}</h2>
                                    <img src="/assets/images/reaction/share.png" width="35" class="img-fluid rounded-circle">
                                    <div>+3</div>
                                </div>
                            </div>
                        </div>

                        <div class="mx-auto mx-lg-3 my-3 my-md-auto"></div>

                        <div class="d-flex justify-content-center">
                            <div class="text-center mx-3" data-toggle="tooltip" title="Bao gồm điểm tương tác trên ảnh đại diện cá nhân và bạn bè được tag">
                                <h1 class="font-weight-normal text-info" data-plugin="counterup">{{ $posts[$i]->point }}</h1>
                                <h5>tổng điểm</h5>
                            </div>   
                        </div>
                    </div>
                </div>
            </a>
            @endfor
        </div><!-- end col -->
    </div>
@endif

<style>
    .rank-title-wrapper{
        max-width: 250px;
    }

    @media only screen and (max-width: 767px) {
        .rank-title-wrapper{
            max-width: 100%;
        }
    }
</style>
@endsection
