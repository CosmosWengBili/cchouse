<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#main">{{$main_title}}</a>
    </li>

    {{ $relation_titles }}
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <div class="tab-pane container active" id="main">
        <div class="card">
            <div class="card-body">
                {{$main_content}}
            </div>
        </div>
    </div>

    {{ $relation_contents }}
</div>
