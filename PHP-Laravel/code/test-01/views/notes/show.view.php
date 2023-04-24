<?php require base_bath('views/partials/head.php')?>
<?php require base_bath('views/partials/nav.php');?>
<?php require base_bath('views/partials/banner.php')?>
<main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <p class="mb-6">
            <a href="/notes" class="text-blue-500 underline ">
                go back ..
            </a>
        </p>
        <p class="w-full md:w-auto">
            <?= htmlspecialchars($note["body"]) ?>
        </p>
        <form class="mt-6" method="POST" action="/note">
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="id" value="<?= $note['id'] ?>">
            <button class="text-sm text-red-500">Delete</button>
        </form>


        <footer class="mt-6">

            <a href="/note/edit?id=<?= $note['id'] ?>"
               class="rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >
                Edit
            </a>

        </footer>


    </div>
</main>
<?php require base_bath('views/partials/footer.php') ?>