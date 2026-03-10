<?php $pager->setSurroundCount(2);?>

<nav class="pagination-rounded pt-4">
    <ul class="pagination">
        <li class="page-item"><a class="page-link" href="<?= $pager->getFirst(); ?>"><i class="fa fa-chevron-double-left"></i></a></li>
        <li class="page-item"><a class="page-link" href="<?= $pager->getPrevious() ?>"><i class="fa fa-chevron-left"></i></a></li>
        <?php if ($pager->hasPrevious()) : ?><?php endif; ?>
        <?php foreach ($pager->links() as $link) : ?>
            <?php if ($link['active']) {
                $active = 'active';
            } else {
                $active = '';
            } ?>
            <li class="page-item <?= $active; ?>"><a class="page-link" href="<?= $link['uri']; ?>"><?= $link['title'] ?></a></li>
        <?php endforeach; ?>
        <li class="page-item"><a class="page-link" href="<?= $pager->getNext(); ?>"><i class="fa fa-chevron-right"></i></a></li>
        <li class="page-item"><a class="page-link" href="<?= $pager->getLast(); ?>"><i class="fa fa-chevron-double-right"></i></a></li>
        <?php if ($pager->hasNext()) : ?><?php endif; ?>
    </ul>
</nav>