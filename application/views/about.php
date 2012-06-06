<h1>This is the about page.</h1>
<h2>Wasn't it easy to load?</h2>

<h3>People</h3>
<?php foreach ($people as $person) : ?>
    {{ $person->name }}<br>
<?php endforeach; ?>
