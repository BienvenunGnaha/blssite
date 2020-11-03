@extends(activeTemplate() .'layouts.master')

@section('content')
@include(activeTemplate() .'layouts.breadcrumb')



    <section class="contact-section padding-bottom padding-top">
     <div class="container">
          <div class="row">
              <div class="col-12">
                  <article>
                      @php 
                          $bl = (new \App\Classes\TranslateFrontend())->translate($rgpd);
                      @endphp
                      <div class="post-item post-classic post-details">
                          <div class="post-content">
                              <div class="blog-header">
                                  <h6 class="title">
                                      @lang($bl->title)
                                  </h6>
                              </div>
                              <div class="entry-content">
                                 <p>
                                    @php echo $bl->body; @endphp
                                 </p>
                              </div>
                          </div>
                      </div>
                  </article>
              </div>
          </div>
     </div>
   </section>    
@endsection