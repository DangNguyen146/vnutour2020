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

<div class="col-12 ">
    <h4>
        Chúng ta là bạn bè của nhau đã kết nối với nhau như thế nào
    </h4>
    <div class="card card-body bg-white">
        <p class="card-text">
            <div id="tagged-network" style="height:80vh"></div>
        </p>
    </div>
</div>
    

@endsection

@section('script')
{{-- <link href="https://unpkg.com/vis-network@7.4.2/dist/vis-network.min.css" rel="stylesheet" type="text/css"/> --}}
<script src="https://unpkg.com/vis-network@7.4.2/dist/vis-network.min.js"></script>


<script type="text/javascript">
    // create an array with nodes
    var nodes = new vis.DataSet({!! $nodes!!});
  
    // create an array with edges
    var edges = new vis.DataSet({!! $edges !!});
  
    // create a network
    var container = document.getElementById('tagged-network');
    var data = {
      nodes: nodes,
      edges: edges
    };
    var options = {
        autoResize: true,
        height: '100%',
        width: '100%',
        nodes: {
          shape: 'dot',
          scaling:{
            label: {
              min:8,
              max:20
            }
          }
        },
        "edges": {
            "smooth": {
            "forceDirection": "none",
            "roundness": 0.85
            }
        }
    };
    var network = new vis.Network(container, data, options);
  </script>
@endsection



