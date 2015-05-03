<main>
    <div class="jumbotron col-lg-10 col-lg-offset-1" style="background-image: url('/content/images/home.jpg'); background-size: cover;">
        <div class="bs-component">
            <h2 class="well well-sm">We don't remember days, we remember moments...</h2>
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
                    <li><a href="/home/publicAlbums">All</a></li>
                    <li class="divider"></li>
                    <?php foreach($this->categories as $category): ?>
                        <li>
                            <a href="/home/publicAlbums?categoryId=<?php echo $category['id']?>"><?php $this->renderText($category['name']);?></a>
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
                                <img src="/content/images/album-icon.png" alt="album-icon"/>
                                <div>
                                    <span><?php $this->renderText($album['name']); ?></span>
                                    <div>
                                        <span>Likes </span>
                                        <span class="badge"><?php $this->renderText($album['likes']); ?></span>
                                        <?php if($this->isLoggedIn && $album['canBeLiked'] == 0): ?>
                                            <span class="label label-primary">You like it</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if($this->isLoggedIn && $album['canBeLiked'] == 1): ?>
                                        <div>
                                            <a href="/albums/like/<?php $this->renderText($album['id']); ?>" class="btn btn-primary">Like</a>
                                        </div>
                                    <?php endif; ?>
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
                <ul class="pager col-lg-12">
                    <li><a href="#">Previous</a></li>
                    <li><a href="#">Next</a></li>
                </ul>
            </div>
        </div>
    </div>
</main>