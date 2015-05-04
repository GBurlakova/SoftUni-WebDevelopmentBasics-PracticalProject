<main>
    <div class="jumbotron col-lg-10 col-lg-offset-1" style="background-image: url('/photo-album/content/images/home.jpg'); background-size: cover;">
        <div class="bs-component">
            <h2 class="grey-container">We don't remember days, we remember moments...</h2>
        </div>
    </div>
    <div class="bs-component col-lg-12">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#public" data-toggle="tab">Public albums</a></li>
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
                    Categories <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="/photo-album/home/publicAlbums">All</a></li>
                    <li class="divider"></li>
                    <?php foreach($this->categories as $category): ?>
                        <li>
                            <a href="/photo-album/home/publicAlbums?categoryId=<?php echo $category['id']?>"><?php $this->renderText($category['name']);?></a>
                        </li>
                    <?php endforeach;?>
                </ul>
            </li>
        </ul>
        <div id="myTabContent" class="tab-content col-lg-10 col-lg-offset-1">
            <div class="tab-pane fade active in" id="public">
                <div class="text-center">
                    <?php if($this->publicAlbums):
                        foreach($this->publicAlbums as $album): ?>
                            <div class="text-center col-lg-4">
                                <div class="photo-album">
                                    <img src="/photo-album/content/images/user-album.png" alt="album-icon"/>
                                </div>
                                <div>
                                    <span><?php $this->renderText($album['name']); ?></span>
                                    <div>
                                        <span>Likes </span>
                                        <span class="badge"><?php $this->renderText($album['likes']); ?></span>
                                        <?php if($this->isLoggedIn && $album['canBeLiked'] == 0): ?>
                                            <span class="label label-primary">You like it</span>
                                        <?php endif; ?>
                                        <?php if($this->isLoggedIn && $album['canBeLiked'] == 1): ?>
                                            <span>
                                                <a href="/photo-album/albums/like/<?php $this->renderText($album['id']); ?>" class="label label-success">Like</a>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="panel panel-primary margin">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Comments</h3>
                                    </div>
                                    <div class="panel-body" style="min-height: 150px; max-height: 150px; overflow-y: auto;">
                                        <?php if($album['comments']):
                                        foreach($album['comments'] as $comment): ?>
                                            <div class="comment">
                                                <div class="comment-body"><?php $this->renderText($comment['text']); ?></div>
                                                <span>User: </span><span class="label label-info"><?php $this->renderText($comment['username']); ?></span>
                                                <span>Date: </span><span><?php $this->renderText(date_format(date_create($comment['date']), 'd/m/Y')); ?></span>
                                            </div>
                                        <?php endforeach;
                                        else: ?>
                                            <div class="bs-component text-center">
                                                <h3 class="well well-sm">No comments yet</h3>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach;
                        ?>
                    <?php else: ?>
                        <div class="bs-component text-center">
                            <h2 class="well well-sm">No albums found</h2>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-12 text-center">
                    <ul class="pagination">
                        <li class="disabled"><a href="#">«</a></li>
                        <?php for($page = 1; $page <= $this->pagesCount; $page++): ?>
                            <li><a href="/photo-album/home/publicAlbums/<?php echo $page; ?>"><?php echo $page; ?></a></li>
                        <?php endfor; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</main>