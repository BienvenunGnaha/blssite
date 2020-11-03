@extends(activeTemplate() .'layouts.master')

@section('content')
    @include(activeTemplate() .'layouts.breadcrumb')
    <section class="faq-section padding-bottom padding-top">
        <div class="container">
            <div class="faq-wrapper-two">
                @foreach($faqs as $data)
                @php 
                    $ts = (new \App\Classes\TranslateFrontend())->translate($data);
                @endphp
                <div class="faq-item-two">
                    <div class="icon">
                        <i class="flaticon-discuss-issue"></i>
                    </div>
                    <div class="faq-content">
                        <h6 class="title">@lang($ts->title)</h6>
                        <p>
                            @php echo $ts->body; @endphp
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

@endsection
