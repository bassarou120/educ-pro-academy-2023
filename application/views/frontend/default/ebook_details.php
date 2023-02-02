<?php
$ebook_details = $this->ebook_model->get_ebook_by_id($ebook_id)->row_array();
$instructor_details = $this->user_model->get_all_user($ebook_details['user_id'])->row_array();
$category_details = $this->ebook_model->get_categories($ebook_details['category_id'])->row_array();
$path = base_url('uploads/ebook/file/ebook_preview/'.$ebook_details['preview']);
$totoalPages = countPages($path);

function countPages($path) {
    $pdftext = file_get_contents($path);
    $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);
    return $num;
}
                              
?>

<section class="category-header-area"
<?php $ebook_banner = 'uploads/ebook/banner/'.$ebook_details['banner']; ?>
<?php if(!file_exists($ebook_banner)){ $ebook_banner = 'uploads/system/ebook_page_banner.png'; } ?>
    style="background-image: url('<?php echo base_url($ebook_banner); ?>');">
    <div class="image-placeholder-1"></div>
    <div class="container-lg breadcrumb-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item display-6 fw-bold">
                    <a href="<?php echo site_url('ebook'); ?>">
                        <?php echo site_phrase('ebooks'); ?>
                    </a>
                </li>
                <li class="breadcrumb-item active text-light display-6 fw-bold">
                    <?php echo $ebook_details['title']; ?>
                </li>
            </ol>
        </nav>
    </div>
</section>

<section class="ebook-header-area">
    <div class="container">
        <div class="row bg-white mt-4 py-5 ebook-shadow d-flex justify-content-center">
            <div class="col-lg-4  d-grid justify-content-center">
                <div class="border p-4">
                    <img height='300px' width='200px'
                        src="<?php echo $url = $this->ebook_model->get_ebook_thumbnail_url($ebook_details['ebook_id']); ?>">
                </div>
            </div>
            <div class="col-lg-4 text-center text-lg-start ">
                <h4 class="text-sm-center text-lg-start"><?php echo $ebook_details['title'] ?></h4>
                <p><i><?php echo get_phrase('created_by') ?></i>
                    <a class="text-14px fw-600 text-decoration-none"
                        href="<?php echo site_url('home/instructor_page/' . $ebook_details['user_id']); ?>"><?php echo $instructor_details['first_name'] . ' ' . $instructor_details['last_name']; ?></a>

                </p>
                <p><?php echo get_phrase('publication_name : ') ?>
                    <span><?php echo $ebook_details['publication_name'] ?></span>
                </p>
                <p><?php echo get_phrase('published_date : ') ?><span><?php echo  date('D, d-M-Y', $ebook_details['added_date']); ?></span>
                </p>
                <p><?php echo get_phrase('category_name : ') ?><span><?php echo $category_details['title'] ?></span></p>
                <div class="rating-row">
                    <?php
                        $total_rating =  $this->ebook_model->get_ratings($ebook_details['ebook_id'], true)->row()->rating;
                        $number_of_ratings = $this->ebook_model->get_ratings($ebook_details['ebook_id'])->num_rows();
                        if ($number_of_ratings > 0) {
                        $average_ceil_rating = ceil($total_rating / $number_of_ratings);
                        } else {
                        $average_ceil_rating = 0;
                        }

                    for ($i = 1; $i < 6; $i++) : ?>
                    <?php if ($i <= $average_ceil_rating) : ?>
                    <i class="fas fa-star filled" style="color: #f5c85b;"></i>
                    <?php else : ?>
                    <i class="fas fa-star"></i>
                    <?php endif; ?>
                    <?php endfor; ?>
                    <span
                        class="d-inline-block average-rating"><?php echo $average_ceil_rating; ?></span><span>(<?php echo $number_of_ratings . ' ' . site_phrase('ratings'); ?>)</span>

                </div>
                <div class="d-flex justify-content-center justify-content-md-start align-items-center">
                    <?php if($ebook_details['is_free']): ?>
                        <h3 class="text-center text-lg-start"><?php echo site_phrase('free'); ?></h3>
                    <?php elseif($ebook_details['discount_flag']): ?>
                        <del><?php echo currency($ebook_details['price']); ?></del>
                        <span class="ms-2">
                            <h3 class="text-center text-lg-start"><?php echo currency($ebook_details['discounted_price']); ?></h3>
                        </span>
                    <?php else: ?>
                        <h3 class="text-center text-lg-start"><?php echo currency($ebook_details['price']); ?></h3>
                    <?php endif ?>
                </div>

                <div>

                    <button class="btn btn-block btn-outline-info" data-bs-toggle="modal"
                        data-bs-target="#ebookModal"><?php echo get_phrase('read_preview') ?></button>
                    <?php if($ebook_details['is_free']): ?>
                        <a href="<?php echo base_url('addons/ebook/download_ebook_file/'.$ebook_details['ebook_id']) ?>" class="btn btn-warning" type="button"><?php echo site_phrase('free_download'); ?></a>
                    <?php else: ?>
                        <?php if($this->db->get_where('ebook_payment', array('user_id' => $this->session->userdata('user_id'), 'ebook_id' => $ebook_details['ebook_id']))->num_rows() > 0): ?>
                            <a href="<?php echo base_url('home/my_ebooks') ?>" class="btn btn-warning"
                            type="button"
                            id="course_<?php echo $ebook_details['ebook_id']; ?>"><?php echo site_phrase('already_purchased'); ?></a>
                        <?php else: ?>
                            <a href="<?php echo base_url('ebook/buy/'.$ebook_details['ebook_id']) ?>" class="btn btn-warning"
                            type="button"
                            id="course_<?php echo $ebook_details['ebook_id']; ?>"><?php echo site_phrase('buy_now'); ?></a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Modal -->
                    <div class="modal fade" id="ebookModal" tabindex="-1" aria-labelledby="ebookModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="ebook-modal d-grid justify-content-center">
                                    <?php if(!empty($ebook_details['preview'])): ?>
                                    <object
                                        data="<?php echo base_url('uploads/ebook/file/ebook_preview/'.$ebook_details['preview'].'#toolbar=0') ?>"
                                        height="100%" width="800px"></object>
                                    <?php else: ?>
                                        <div class="w-100 text-center pt-5 mt-5">
                                            <img width="200px" class="" src="<?php echo site_url('assets/global/image/no-preview-available.png'); ?>">
                                        </div>
                                    <?php endif ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>
<style>
tr,
th,
td {
    padding: 10px 20px;

    border: 1px solid #dddddd;
}

th {
    background-color: #f1f2f4;
}

.h-10 {
    height: 10% !important;
}

.w-10 {
    width: 10% !important;

}

.ebook-modal {
    position: relative;
    flex: 1 1 auto;
    padding: 1rem;
    min-height: 80vh;
}
</style>
<section>
    <div class="container">
        <div class="row bg-white mt-4 py-8 p-4 ebook-shadow d-flex justify-content-center">
            <h4 class="mb-4"><?php echo get_phrase('book_specification_and_summary') ?></h4>

            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary"
                        type="button" role="tab" aria-controls="home"
                        aria-selected="true"><?php echo get_phrase('summary') ?></button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button"
                        role="tab" aria-controls="home"
                        aria-selected="true"><?php echo get_phrase('specification') ?></button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="author-tab" data-bs-toggle="tab" data-bs-target="#author" type="button"
                        role="tab" aria-controls="author"
                        aria-selected="false"><?php echo get_phrase('author') ?></button>
                </li>

            </ul>
            <div class="tab-content mt-4" id="myTabContent">
                <div class="tab-pane fade show active" id="summary" role="tabpanel" aria-labelledby="author-tab">
                    <div>
                        <h5><?php echo $ebook_details['title'] ?></h5>
                        <p><?php echo htmlspecialchars_decode($ebook_details['description']) ?></p>
                    </div>

                </div>

                <div class="tab-pane fade " id="home" role="tabpanel" aria-labelledby="home-tab">
                    <table style="width:100%">
                        <tr>
                            <th style="width: 30%">Title</th>

                            <td style="width:70%"><?php echo $ebook_details['title'] ?></td>


                        </tr>
                        <tr>
                            <th>Author</td>
                            <td colspan="2">
                                <?php echo $instructor_details['first_name']." ".$instructor_details['last_name'] ?>
                            </td>

                        </tr>
                        <tr>
                            <th>Publisher</td>
                            <td><?php echo $ebook_details['publication_name'] ?></td>

                        </tr>
                        <tr>
                            <th>Edition</td>
                            <td><?php echo $ebook_details['edition'] ?></td>

                        </tr>
                        <tr>

                            <th>No. of page</td>
                            <td><?php echo $totoalPages ?></td>

                        </tr>
                    </table>
                </div>
                <div class="tab-pane fade" id="author" role="tabpanel" aria-labelledby="author-tab">
                    <div class="d-flex align-items-center">
                        <img class="rounded-circle w-10 h-10"
                            src="<?php echo $this->user_model->get_user_image_url($ebook_details['user_id']) ?>" alt="">
                        <div class="ms-4">
                            <h3><?php echo $instructor_details['first_name']." ".$instructor_details['last_name'] ?>
                            </h3>
                            <p><?php echo $instructor_details['biography'] ?></p>

                        </div>

                    </div>

                </div>
            </div>


        </div>
    </div>
</section>


<div class="container">
    <div class="row">
        <div class="col-lg-12 order-last order-lg-first radius-10 mt-4 bg-white">



            <div class="row d-flex justify-content-center">
                <div class="col-12 px-4"><h3 class="my-4"><?php echo get_phrase("other_related_ebooks") ?></h3></div>

                <?php
                $this->db->limit(5);
                $other_related_ebooks = $this->ebook_model->get_ebooks($ebook_details['category_id'])->result_array();
                foreach ($other_related_ebooks as $other_related_ebook) : ?>

                <?php if($other_related_ebook['ebook_id'] != $ebook_details['ebook_id'] && $other_related_ebook['is_active']): ?>


                <div class="col-md-6 col-xl-3">


                    <div class="card ebook-card pt-4 px-4 margin-right">



                        <img src="<?php echo $this->ebook_model->get_ebook_thumbnail_url($other_related_ebook['ebook_id']); ?>"
                            class="card-img-top position-relative image" alt="ebook image" height="auto">




                        <div class="middle">


                            <a href="<?php echo base_url('ebook/buy/'.$other_related_ebook['ebook_id']) ?>"
                                class="buy-button"><?php echo get_phrase('buy_now') ?></a>
                        </div>

                        <div class="card-body text-center">

                            <div>
                                <h5><?php echo $other_related_ebook['title'] ?></h3>

                                    <p> <i>by</i>
                                        <?php $instructor_details = $this->user_model->get_all_user($other_related_ebook['user_id'])->row_array(); ?>
                                        <?php echo $instructor_details['first_name'].' '.$instructor_details['last_name']; ?>
                                    </p>

                            </div>

                            <div class="rating-row">
                                <?php
                                                    $total_rating =  $this->ebook_model->get_ratings($other_related_ebook['ebook_id'], true)->row()->rating;
                                                    $number_of_ratings = $this->ebook_model->get_ratings($other_related_ebook['ebook_id'])->num_rows();
                                                    if ($number_of_ratings > 0) {
                                                    $average_ceil_rating = ceil($total_rating / $number_of_ratings);
                                                    } else {
                                                    $average_ceil_rating = 0;
                                                    }

                                                for ($i = 1; $i < 6; $i++) : ?>
                                <?php if ($i <= $average_ceil_rating) : ?>
                                <i class="fas fa-star filled" style="color: #f5c85b;"></i>
                                <?php else : ?>
                                <i class="fas fa-star"></i>
                                <?php endif; ?>
                                <?php endfor; ?>
                                <span
                                    class="d-inline-block average-rating"><?php echo $average_ceil_rating; ?></span><span>(<?php echo $number_of_ratings . ' ' . site_phrase('ratings'); ?>)</span>

                            </div>



                        </div>
                        <div class="view-details">
                            <a href="<?php echo site_url('ebook/ebook_details/'.rawurlencode(slugify($other_related_ebook['title'])).'/'.$other_related_ebook['ebook_id']) ?>"
                                class="d-block text-white">View details</a>
                        </div>

                    </div>


                </div>
                <?php endif ?>
                <?php endforeach; ?>

            </div>


            <div class="row">
                <div class="col-xl-6">
                    <div class="about-instructor-box mt-5 pb-3 px-4">
                        <div class="about-instructor-title">
                            <?php echo site_phrase('about_instructor'); ?>

                            <?php
                            $instructor_details = $this->user_model->get_all_user($ebook_details['user_id'])->row_array();

                             ?>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-md-4 top-instructor-img w-sm-100">
                                <a href="<?php echo site_url('home/instructor_page/'.$instructor_details['id']); ?>">
                                    <img src="<?php echo $this->user_model->get_user_image_url($instructor_details['id']); ?>"
                                        width="100%">
                                </a>
                            </div>
                            <div class="col-md-8 top-instructor-details text-center text-md-start">
                                <h4 class="mb-1 fw-600 v"><a class="text-decoration-none"
                                        href="<?php echo site_url('home/instructor_page/'.$instructor_details['id']); ?>"><?php echo $instructor_details['first_name'].' '.$instructor_details['last_name']; ?></a>
                                </h4>
                                <p class="fw-500 text-14px w-100 ellipsis-line-2"><?php echo $instructor_details['title']; ?></p>
                                <div class="rating">
                                    <div class="d-inline-block">
                                        <span
                                            class="text-dark fw-800 text-muted ms-1 text-13px"><?php  echo $this->ebook_model->get_instructor_wise_ebook_ratings($instructor_details['id'], 'ebook')->num_rows().' '.site_phrase('reviews'); ?></span>

                                        |
                                        <span class="text-dark fw-800 text-14px text-muted">
                                            <?php echo $this->ebook_model->get_instructor_wise_ebooks($instructor_details['id'])->num_rows().' '.site_phrase('ebooks'); ?>
                                        </span>
                                    </div>
                                </div>
                                <?php $skills = explode(',', $instructor_details['skills']); ?>
                                <?php foreach($skills as $skill): ?>
                                <span class="badge badge-sub-warning text-12px my-1 py-2"><?php echo $skill; ?></span>
                                <?php endforeach; ?>


                                <div class="description ellipsis-line-3">
                                    <?php echo strip_tags($instructor_details['biography']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="student-feedback-box mt-5 pb-3 px-4">
                        <div class="student-feedback-title">
                            <?php echo site_phrase('ebook_review'); ?>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="average-rating ms-auto me-auto float-md-start mb-sm-4">
                                    <div class="num">
                                        <?php
                                       
                                    $total_rating =  $this->ebook_model->get_ratings($ebook_details['ebook_id'], true)->row()->rating;
                                    $number_of_ratings = $this->ebook_model->get_ratings($ebook_details['ebook_id'])->num_rows();
                                    if ($number_of_ratings > 0) {
                                        $average_ceil_rating = ceil($total_rating / $number_of_ratings);
                                    } else {
                                        $average_ceil_rating = 0;
                                    }
                                    echo $average_ceil_rating;
                          ?>
                                    </div>
                                    <div class="rating">
                                        <?php for ($i = 1; $i < 6; $i++) : ?>
                                        <?php if ($i <= $average_ceil_rating) : ?>
                                        <i class="fas fa-star filled" style="color: #f5c85b;"></i>
                                        <?php else : ?>
                                        <i class="fas fa-star" style="color: #abb0bb;"></i>
                                        <?php endif; ?>
                                        <?php endfor; ?>
                                    </div>
                                    <div class="title text-15px fw-700"><?php echo $number_of_ratings; ?>
                                        <?php echo site_phrase('reviews'); ?></div>
                                </div>
                                <div class="individual-rating">
                                    <ul>
                                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                                        <li>
                                            <div>
                                                <span class="rating">
                                                    <?php for ($j = 1; $j <= (5 - $i); $j++) : ?>
                                                    <i class="fas fa-star"></i>
                                                    <?php endfor; ?>
                                                    <?php for ($j = 1; $j <= $i; $j++) : ?>
                                                    <i class="fas fa-star filled"></i>
                                                    <?php endfor; ?>

                                                </span>
                                            </div>

                                            <div class="progress ms-2 mt-1">

                                                <div class="progress-bar"
                                                    style="width: <?php  echo $this->ebook_model->get_percentage_of_specific_rating($i, 'ebook', $ebook_id); ?>%">
                                                </div>
                                            </div>
                                            <span class="d-inline-block ps-2 text-15px fw-500">
                                                (<?php echo $this->db->get_where('ebook_reviews', array( 'ebook_id' => $ebook_id, 'rating' => $i))->num_rows(); ?>)
                                            </span>
                                        </li>
                                        <?php endfor; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 student-feedback-box px-4">
                    <div class="reviews mt-5">
                        <h3><?php echo site_phrase('reviews'); ?></h3>
                        <ul>
                            <?php
                              $ratings = $this->ebook_model->get_ratings($ebook_id)->result_array();
                              foreach ($ratings as $rating) :
                              ?>
                            <li>
                                <div class="row">
                                    <div class="col-auto">
                                        <div class="reviewer-details clearfix">
                                            <div class="reviewer-img">
                                                <img src="<?php echo $this->user_model->get_user_image_url($rating['user_id']); ?>"
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="review-time">
                                            <div class="reviewer-name fw-500">
                                                <?php
                              $user_details = $this->ebook_model->get_user($rating['user_id'])->row_array();
                              echo $user_details['first_name'] . ' ' . $user_details['last_name'];
                              ?>
                                            </div>
                                            <!-- <div class="time text-11px text-muted">
                              <?php echo date('d/m/Y', $rating['date_added']); ?>
                            </div> -->
                                        </div>
                                        <div class="review-details">
                                            <div class="rating">
                                                <?php
                              for ($i = 1; $i < 6; $i++) : ?>
                                                <?php if ($i <= $rating['rating']) : ?>
                                                <i class="fas fa-star filled" style="color: #f5c85b;"></i>
                                                <?php else : ?>
                                                <i class="fas fa-star" style="color: #abb0bb;"></i>
                                                <?php endif; ?>
                                                <?php endfor; ?>
                                            </div>
                                            <div class="review-text text-13px">
                                                <?php echo $rating['comment']; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
</section>



<style media="screen">
.embed-responsive-16by9::before {
    padding-top: 0px;
}
</style>
<script type="text/javascript">
function handleCartItems(elem) {
    url1 = '<?php echo site_url('home/handleCartItems'); ?>';
    url2 = '<?php echo site_url('home/refreshWishList'); ?>';
    $.ajax({
        url: url1,
        type: 'POST',
        data: {
            course_id: elem.id
        },
        success: function(response) {
            $('#cart_items').html(response);
            if ($(elem).hasClass('active')) {
                $(elem).removeClass('active')
                $(elem).text("<?php echo site_phrase('add_to_cart'); ?>");
            } else {
                $(elem).addClass('active');
                $(elem).addClass('active');
                $(elem).text("<?php echo site_phrase('added_to_cart'); ?>");
            }
            $.ajax({
                url: url2,
                type: 'POST',
                success: function(response) {
                    $('#wishlist_items').html(response);
                }
            });
        }
    });
}

function handleBuyNow(elem) {

    url1 = '<?php echo site_url('home/handleCartItemForBuyNowButton'); ?>';
    url2 = '<?php echo site_url('home/refreshWishList'); ?>';
    urlToRedirect = '<?php echo site_url('home/shopping_cart'); ?>';
    var explodedArray = elem.id.split("_");
    var course_id = explodedArray[1];

    $.ajax({
        url: url1,
        type: 'POST',
        data: {
            course_id: course_id
        },
        success: function(response) {
            $('#cart_items').html(response);
            $.ajax({
                url: url2,
                type: 'POST',
                success: function(response) {
                    $('#wishlist_items').html(response);
                    toastr.success('<?php echo site_phrase('please_wait') . '....'; ?>');
                    setTimeout(
                        function() {
                            window.location.replace(urlToRedirect);
                        }, 1000);
                }
            });
        }
    });
}

function handleEnrolledButton() {
    $.ajax({
        url: '<?php echo site_url('home/isLoggedIn?url_history='.base64_encode(current_url())); ?>',
        success: function(response) {
            if (!response) {
                window.location.replace("<?php echo site_url('login'); ?>");
            }
        }
    });
}

function handleAddToWishlist(elem) {
    $.ajax({
        url: '<?php echo site_url('home/isLoggedIn?url_history='.base64_encode(current_url())); ?>',
        success: function(response) {
            if (!response) {
                window.location.replace("<?php echo site_url('login'); ?>");
            } else {
                $.ajax({
                    url: '<?php echo site_url('home/handleWishList'); ?>',
                    type: 'POST',
                    data: {
                        course_id: elem.id
                    },
                    success: function(response) {
                        if ($(elem).hasClass('active')) {
                            $(elem).removeClass('active');
                            $(elem).text("<?php echo site_phrase('add_to_wishlist'); ?>");
                        } else {
                            $(elem).addClass('active');
                            $(elem).text("<?php echo site_phrase('added_to_wishlist'); ?>");
                        }
                        $('#wishlist_items').html(response);
                    }
                });
            }
        }
    });
}

function pausePreview() {
    player.pause();
}

$('.course-compare').click(function(e) {
    e.preventDefault()
    var redirect_to = $(this).attr('redirect_to');
    window.location.replace(redirect_to);
});

function go_course_playing_page(course_id, lesson_id) {
    var course_playing_url = "<?php echo site_url('home/lesson/'.slugify($ebook_details['title'])); ?>/" + course_id +
        '/' + lesson_id;

    $.ajax({
        url: '<?php echo site_url('home/go_course_playing_page/'); ?>' + course_id,
        type: 'POST',
        success: function(response) {
            if (response == 1) {
                window.location.replace(course_playing_url);
            }
        }
    });
}
</script>

<style>
.ebook-card {
    position: relative;
    width: 100%;
    min-height: 460px;
    margin-bottom: 20px;
}

.image {
    opacity: 1;
    display: block;
    width: 100%;
    height: auto;
    transition: .5s ease;
    backface-visibility: hidden;

}


.margin-right {
    margin-right: 10px;
}

.buy-button {
    display: block;
    width: 100%;
    text-align: center;
    background-color: #999933;
    padding: 10px;
    color: #fff;
}

.buy-button:hover {
    color: #fff;
}

.middle {
    transition: .5s ease;
    opacity: 0;
    position: absolute;
    /* display: block; */
    top: 32%;
    width: inherit;
    right: 0;
    /* margin-left: 30px; */
    padding: 24px;
}

.low {
    transition: .5s ease;
    opacity: -1;
    position: relative;
    bottom: -20px;
}




.ebook-card:hover .image {
    opacity: 0.3;
}

.ebook-card:hover .middle {
    /* opacity: 0.3; */
    opacity: 1;
}

.view-details {
    transition: .5s ease;
    display: none;
    background: #ffc84bed;
    width: -webkit-fill-available;

    padding: 12px;
    position: absolute;
    text-align: center;

    bottom: 0;
    right: 0;
}

.ebook-card:hover .view-details {


    display: block;
}

.text {
    background-color: #04AA6D;
    color: white;
    font-size: 16px;
    padding: 16px 32px;
}
</style>