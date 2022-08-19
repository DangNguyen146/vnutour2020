<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>{{$title ?? "Hành trình thực tế"}} - VNU TOUR 2020</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Hành trình thực tế khám phá khu đô thị Đại học Quốc gia TPHCM được tổ chức bởi Đoàn khoa Mạng máy tính và Truyền thông" name="description" />
        <meta content="LuanVT" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <meta property="og:url"            content="https://www.facebook.com/VNUTour" />
        <meta property="og:type"           content="website" />
        <meta property="og:title"          content="{{$title ?? "Hành trình thực tế VNU Tour"}} - Đoàn khoa MMT&TT" />
        <meta property="og:description"    content="Hành trình Khám phá khu đô thị Đại học Quốc gia" />
        <meta property="og:image"          content="https://vnutour.suctremmt.com/assets/images/vnutour.jpg" />
        <meta property="og:locale"         content="vi_VN" />

        <!-- App favicon -->
        <link type="image/png" rel="icon" rel="shortcut icon" href="/assets/images/favicon.png">

        <!-- App css -->
        <link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="/assets/css/icons.min.css" rel="stylesheet" type="text/css" />

        <link href="/assets/css/app.min.css"  rel="stylesheet" type="text/css" />

        {{-- <link href="/assets/libs/toastr/toastr.min.css" rel="stylesheet" type="text/css" /> --}}

    </head>

    <body class="left-side-menu-dark left-side-menu-sm">
        <!-- Begin page -->
        <div id="fb-root"></div>
        <div id="wrapper">
            @include('layout/topbar')
            @include("layout/leftsidebar")

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                        @yield('content')      
                        
                    </div> <!-- container -->

                </div> <!-- content -->
                <footer class="footer position-fixed">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                               2020 &copy; Thực hiện bởi <a href="https://suctremmt.com">Đoàn khoa Mạng máy tính và Truyền thông</a> 
                            </div>
                            <div class="col-md-6 text-right">
                                {{-- <small id="sync-container">Có thay đổi cách đây <span id="lastSync"></span></small> --}}
                             </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <!-- Load Facebook SDK for JavaScript -->
    

        <script src="/assets/js/vendor.min.js"></script>
        <script src="/assets/js/app.min.js"></script>

        <script>
            if(!isNaN(lastSync) && lastSync != undefined){
                lastSync = new Date().getTime() - lastSync;
                lastSync = parseInt(lastSync/1000);

                setInterval(() => {
                    if(lastSync < 4){
                        $("#lastSync").html("vài giây trước")
                    }else if(lastSync < 60){
                        $("#lastSync").html(lastSync +" giây trước")
                    }else if(lastSync < 3600){
                        $("#lastSync").html(parseInt(lastSync/60) +" phút trước")
                    }else if(lastSync < 86400){
                        $("#lastSync").html(parseInt(lastSync/3600) +" giờ trước")
                    }else{
                        $("#lastSync").html(parseInt(lastSync/86400) +" ngày trước")
                    }

                    lastSync ++;
                }, 1000);
            }else{
                $('#sync-container').hide();
            }
            

            //Notification
            var notificationList=[
                "Chương trình VNU Tour 2020 đã quay trở lại",
                'Hành trình khám phá khu đô thị Đại học quốc gia Thành phố Hồ Chí Minh',
                "Các đội thi đến từ các trường đại học khác nhau tại TP.HCM",
                "Ai sẽ giành lấy giải thưởng 1.515.000 đồng từ ban tổ chúc",
                "Trải qua hai vòng thi gây cấn",
                "Hãy Like và Follow fanpage <a href='https://facebook.com/VNUTour' target='__blank'>VNU Tour</a> để nhận thông tin mới nhất",
                "Vòng loại chương trình sẽ được diễn ra vào ngày 09/11/2020",
            ];


            var allowChangeNotification = true;
            $("#notification")
            .mouseover(function(){
                allowChangeNotification = false;
            })
            .mouseout(function(){
                allowChangeNotification = true;
            })

            var changeNotification = ()=>{
                var noti = notificationList[Math.floor(Math.random()*notificationList.length)]
                if (noti.length*8 > $("#notification").width()){
                    noti = '<marquee onmouseover="this.stop();" onmouseout="this.start();">'+noti+"</marquee>";
                }

                if(allowChangeNotification){
                    $("#notification>div").fadeOut("slow", function(){
                        $("#notification").html("<div>"+ noti + "</div>")
                    });
                }
                setTimeout(changeNotification, noti.length  * 90);
            };
            changeNotification();


            //Time remain
            var timeRemain = {{ (new \Carbon\Carbon(config("app.finish_at")))->timestamp }}000;
            timeRemain = timeRemain - (new Date().getTime());
            timeRemain = parseInt(timeRemain/1000);

            setInterval(() => {
                if(timeRemain < 0){
                    $("#time-remain").html("đã kết thúc")
                }else if(timeRemain < 5){
                    $("#time-remain").html("gần kết thúc")
                }else if(timeRemain < 60){
                    $("#time-remain").html("còn " + timeRemain +" giây")
                }else if(timeRemain < 3600){
                    $("#time-remain").html("còn " +parseInt(timeRemain/60) +" phút")
                }else if(timeRemain < 86400){
                    $("#time-remain").html("còn " +parseInt(timeRemain/3600) +" giờ")
                }else{
                    $("#time-remain").html("còn " +Math.ceil( timeRemain/86400) +" ngày")
                }

                timeRemain --;
            }, 1000);

        </script>

        @yield('script')
        
    </body>
</html>