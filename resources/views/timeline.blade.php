@extends('layout/app')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="page-title">Timeline</h4>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-sm-12">
            <div class="timeline" dir="ltr">
                <article class="timeline-item alt">
                    <div class="time-show first">
                        <a href="#" class="btn btn-primary width-lg">ĐĂNG KÝ</a>
                    </div>
                </article>
                <article class="timeline-item">
                    <div class="timeline-desk">
                        <div class="panel">
                            <div class="panel-body">
                                <span class="arrow-alt"></span>
                                <span class="timeline-icon bg-success"><i class="mdi mdi-circle"></i></span>
                                <h4 class="text-success">Mở đăng ký</h4>
                                <p class="timeline-date text-muted"><small>06-10-2020</small></p>
                                <p>Bắt đầu mở đăng ký tham gia chương trình </p>
                            </div>
                        </div>
                    </div>
                </article>
                <article class="timeline-item ">
                    <div class="timeline-desk">
                        <div class="panel">
                            <div class="panel-body">
                                <span class="arrow"></span>
                                <span class="timeline-icon bg-danger"><i class="mdi mdi-circle"></i></span>
                                <h4 class="text-danger">Đóng đăng ký</h4>
                                <p class="timeline-date text-muted"><small>30-10-2020</small></p>
                                <p>Kết thúc đăng ký tham gia chương trình</p>

                            </div>
                        </div>
                    </div>
                </article>
                <article class="timeline-item">
                    <div class="timeline-desk">
                        <div class="panel">
                            <div class="panel-body">
                                <span class="arrow"></span>
                                <span class="timeline-icon bg-danger"><i class="mdi mdi-circle"></i></span>
                                <h4 class="text-danger">Kết thúc thu lệ phí</h4>
                                <p class="timeline-date text-muted"><small>02-11-2020</small></p>
                                <p>Kết thúc đóng lệ phí tham gia</p>

                            </div>
                        </div>
                    </div>
                </article>

                <article class="timeline-item alt mt-5">
                    <div class="time-show">
                        <a href="#" class="btn btn-primary width-lg">MINIGAME</a>
                    </div>
                </article>
                <article class="timeline-item alt">
                    <div class="timeline-desk">
                        <div class="panel bg-warning ">
                            <div class="panel-body">
                                <span class="arrow"></span>
                                {{-- <span class="timeline-icon bg-warning"><i class="mdi mdi-circle"></i></span> --}}
                                <h4 class="text-white font-weight-bold">Find the differences</h4>
                            </div>
                        </div>
                    </div>
                </article>
                <article class="timeline-item alt">
                    <div class="timeline-desk">
                        <div class="panel">
                            <div class="panel-body">
                                <span class="arrow-alt"></span>
                                <span class="timeline-icon bg-primary"><i class="mdi mdi-circle"></i></span>
                                <h4 class="text-primary timeline-date">15-17/10/2020</h4>
                                {{-- <p class="timeline-date text-muted"><small>15-17/10/2020</small></p> --}}
                                <p>Có kẻ giả mạo đã thay đổi LOGO của VNU Tour, các bạn hãy giúp chúng mình tìm ra những điểm mà hắn đã thay đổi nhé. Cuộc chơi sẽ được diễn ra từ 20:00 ngày 15-10-2020 đến 20:00 ngày 17-10-2020</p>
                            </div>
                        </div>
                    </div>
                </article>
                <article class="timeline-item">
                    <div class="timeline-desk">
                        <div class="panel bg-danger ">
                            <div class="panel-body">
                                <span class="arrow"></span>
                                {{-- <span class="timeline-icon bg-warning"><i class="mdi mdi-circle"></i></span> --}}
                                <h4 class="text-white font-weight-bold">Cuộc thi ảnh "Chúng tôi là..."</h4>
                            </div>
                        </div>
                    </div>
                </article>
                <article class="timeline-item">
                    <div class="timeline-desk">
                        <div class="panel">
                            <div class="panel-body">
                                <span class="arrow"></span>
                                <span class="timeline-icon bg-success"><i class="mdi mdi-circle"></i></span>
                                <h4 class="text-success">Nhận bài dự thi</h4>
                                <p class="timeline-date text-muted"><small>đến 06/11</small></p>
                                <p>Các đội thi chụp cho team mình một tấm ảnh thể hiện được "NÉT RIÊNG" với chủ đề tự chọn. Kèm theo đó là caption siêu ngầu nói lên ý nghĩa SLOGAN của đội của mình.</p>
                            </div>
                        </div>
                    </div>
                </article>
                <article class="timeline-item">
                    <div class="timeline-desk">
                        <div class="panel">
                            <div class="panel-body">
                                <span class="arrow"></span>
                                <span class="timeline-icon bg-success"><i class="mdi mdi-circle"></i></span>
                                <h4 class="text-success">Bắt đầu bình chọn bài dự thi</h4>
                                <p class="timeline-date text-muted"><small>đến 20:00 06/11</small></p>
                                <p>Các đội thi tích cực giới thiệu bài dự thi được Ban tổ chức đăng tải tại fanpage VNU Tour đến bạn bè. Với mỗi lượt quan tâm của bạn bè (like, bộc lộ cảm xúc, chia sẻ), đội dự thi sẽ nhận được điểm cho cuộc thi.</p>
                            </div>
                        </div>
                    </div>
                </article>
                <article class="timeline-item">
                    <div class="timeline-desk">
                        <div class="panel">
                            <div class="panel-body">
                                <span class="arrow"></span>
                                <span class="timeline-icon bg-success"><i class="mdi mdi-circle"></i></span>
                                <h4 class="text-success">Đóng bình chọn bài dự thi</h4>
                                <p class="timeline-date text-muted"><small>đến 08:00 08/11</small></p>
                                <p>Kết thúc bình chọn bài dự thi.</p>
                            </div>
                        </div>
                    </div>
                </article>
                
                <article class="timeline-item alt">
                    <div class="time-show">
                        <a href="#" class="btn btn-primary width-lg">VÒNG LOẠI - 09/11/2020</a>
                    </div>
                </article>

                <article class="timeline-item alt">
                    <div class="timeline-desk">
                        <div class="panel">
                            <div class="panel-body">
                                <span class="arrow"></span>
                                <span class="timeline-icon bg-warning"><i class="mdi mdi-circle"></i></span>
                                <h4 class="text-warning">Check-in</h4>
                                <p class="timeline-date text-muted"><small>10:00</small></p>
                                <p>Địa điểm: Giảng đường 1</p>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="timeline-item alt">
                    <div class="timeline-desk">
                        <div class="panel">
                            <div class="panel-body">
                                <span class="arrow"></span>
                                <span class="timeline-icon bg-warning"><i class="mdi mdi-circle"></i></span>
                                <h4 class="text-warning">Khai mạc</h4>
                                <p class="timeline-date text-muted"><small>11:00</small></p>
                                <p></p>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="timeline-item alt">
                    <div class="timeline-desk">
                        <div class="panel">
                            <div class="panel-body">
                                <span class="arrow-alt"></span>
                                <span class="timeline-icon bg-warning"><i class="mdi mdi-circle"></i></span>
                                <h4 class="text-warning">Bế mạc</h4>
                                <p class="timeline-date text-muted"><small>12:30</small></p>
                                {{-- <p>Download the new updates of Ubold admin dashboard</p> --}}
                            </div>
                        </div>
                    </div>
                </article>
                <article class="timeline-item alt">
                    <div class="time-show">
                        <a href="#" class="btn btn-primary width-lg">VÒNG CHUNG KẾT - 15/11/2020</a>
                    </div>
                </article>
                <article class="timeline-item">
                    <div class="timeline-desk">
                        <div class="panel">
                            <div class="panel-body">
                                <span class="arrow"></span>
                                <span class="timeline-icon bg-danger"><i class="mdi mdi-circle">    </i></span>
                                <h4 class="text-danger">Tập trung</h4>
                                <p class="timeline-date text-muted"><small>07:30</small></p>
                                <p>Địa điểm: Cổng A - Trường ĐH CNTT</p>
                            </div>
                        </div>
                    </div>
                </article>
                <article class="timeline-item">
                    <div class="timeline-desk">
                        <div class="panel">
                            <div class="panel-body">
                                <span class="arrow"></span>
                                <span class="timeline-icon bg-danger"><i class="mdi mdi-circle">    </i></span>
                                <h4 class="text-danger">Kết thúc</h4>
                                <p class="timeline-date text-muted"><small>16:00</small></p>
                            </div>
                        </div>
                    </div>
                </article>

            </div>
        </div>
    </div>
@endsection