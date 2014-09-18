<?php

$this->set([
	'page' => [
		'type' => 'multiple',
		'object' => $t('watchers')
	]
]);

?>
<article class="view-<?= $this->_config['controller'] . '-' . $this->_config['template'] ?> use-list">

	<?php if ($data->count()): ?>
		<table>
			<thead>
				<tr>
					<td data-sort="user" class="user list-sort"><?= $t('User') ?>
					<td data-sort="subject" class="subject list-sort"><?= $t('Subject') ?>
					<td data-sort="created" class="date created list-sort desc"><?= $t('Created') ?>
					<td class="actions">
						<?= $this->form->field('search', [
							'type' => 'search',
							'label' => false,
							'placeholder' => $t('Filter'),
							'class' => 'list-search'
						]) ?>
			</thead>
			<tbody class="list">
				<?php foreach ($data as $item): ?>
					<?php $subject = $item->subject() ?>
				<tr data-id="<?= $item->id ?>">
					<td class="user">
						<?php if ($user): ?>
							<?= $this->html->link($user->number, [
								'controller' => $user->isVirtual() ? 'VirtualUsers' : 'Users',
								'action' => 'edit', 'id' => $user->id,
								'library' => 'base_core'
							]) ?>
						<?php else: ?>
							-
						<?php endif ?>

					<td class="subject">
						<?= $subject->model ?> /
						<?php if ($subject->title): ?>
							<?= $subject->title ?>
						<?php else: ?>
							<?= $subject->id ?>
						<?php endif ?>
					<td class="date created">
						<time datetime="<?= $this->date->format($item->created, 'w3c') ?>">
							<?= $this->date->format($item->created, 'date') ?>
						</time>
					<td class="actions">
						<?= $this->html->link($t('open'), ['id' => $item->id, 'action' => 'edit', 'library' => 'ecommerce_watch'], ['class' => 'button']) ?>
				<?php endforeach ?>
			</tbody>
		</table>
	<?php else: ?>
		<div class="none-available"><?= $t('No items available, yet.') ?></div>
	<?php endif ?>
</article>