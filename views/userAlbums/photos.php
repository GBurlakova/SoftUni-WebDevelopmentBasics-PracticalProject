<main class="col-lg-10 col-lg-offset-1">
    <?php if($this->albumPhotos):
        foreach($this->albumPhotos as $photo): ?>
            <div class="text-center col-lg-4">
                <img class="img-thumbnail more-margin photo" src="/photo-album/content/user-photos/user<?php $this->renderText($photo['userId'].'/'.$photo['name']); ?>" alt="user-photo"/>
                <form action="/photo-album/photos/download" method="post">
                    <input type="hidden" name="photoName" value="<?php $this->renderText($photo['name']); ?>"/>
                    <input type="hidden" name="userId" value="<?php $this->renderText($photo['userId']); ?>"/>
                    <input class="btn-sm btn-primary" type="submit" value="Download"/>
                </form>
                <div class="panel panel-primary margin">
                    <div class="panel-heading">
                        <h3 class="panel-title">Comments</h3>
                    </div>
                    <div class="panel-body" style="min-height: 150px; max-height: 150px; overflow-y: auto;" id="panel-photo-body<?php $this->renderText($photo['id']); ?>">
                        <?php if($photo['comments']):
                            foreach($photo['comments'] as $comment): ?>
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
                <div>
                    <a class="btn btn-success comment-photo-btn" id="comment-photo-btn<?php $this->renderText($photo['id']); ?>">Comment</a>
                </div>
            </div>
        <?php endforeach;
        ?>
    <?php else: ?>
        <div class="bs-component text-center">
            <h2 class="well well-sm">No photos in this album yet</h2>
        </div>
    <?php endif; ?>
</main>