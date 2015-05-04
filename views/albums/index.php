<main class="col-lg-10 col-lg-offset-1">
    <?php if($this->userAlbums):
        foreach($this->userAlbums as $album): ?>
            <div class="text-center col-lg-4">
                <div class="photo-album">
                    <img src="/content/images/user-album.png" alt="album-icon"/>
                </div>
                <div>
                    <span><?php $this->renderText($album['name']); ?></span>
                    <div>
                        <span>Likes </span>
                        <span class="badge"><?php $this->renderText($album['likes']); ?></span>
                    </div>
                    <div>
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
                                endif; ?>
                            </div>
                        </div>
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
</main>