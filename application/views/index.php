<h1>Welcome to SS MVC.</h1>
<h2>A super simple framework designed to improve your workflow.</h2>

<?php foreach ($users as $user) : ?>
    {{$user->name}}<br>
<?php endforeach; ?>
