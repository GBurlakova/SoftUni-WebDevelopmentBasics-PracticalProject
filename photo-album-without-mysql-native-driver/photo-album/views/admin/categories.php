<main>
    <div class="page-header text-center">
        <h2>Categories</h2>
    </div>
        <?php if(isset($this->categories)):
            foreach($this->categories as $category): ?>
                <div class="well col-lg-4 col-lg-offset-4 text-center" id="edit-category-field<?php $this->renderText($category['id']) ?>">
                    <div class="col-lg-12">
                        <h3><?php $this->renderText($category['name']); ?></h3>
                    </div>
                    <div class="margin col-lg-12">
                        <a class="btn btn-danger delete-category-btn" id="delete-category-btn<?php $this->renderText($category['id']); ?>">Delete</a>
                        <a class="btn btn-info edit-category-btn" id="edit-category-btn<?php $this->renderText($category['id']); ?>">Edit</a>
                    </div>
                </div>
        <?php endforeach;
        else: echo 'No categories'; endif;?>
</main>