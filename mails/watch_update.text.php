Hello <?= $user->name ?>,

Products you are watching have become available:

<?php foreach ($products as $product): ?>
	- <?= $product->title ?>
<?php endforeach ?>