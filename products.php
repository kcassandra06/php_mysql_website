<?php

require_once __DIR__ . "/database.php";


// --------------------------------------------------
// ADD IMAGE COLUMN IF IT DOES NOT EXIST
// --------------------------------------------------

$columns = $pdo
    ->query("SHOW COLUMNS FROM products LIKE 'image'")
    ->fetch();

if (!$columns) {

    $pdo->exec(
        "ALTER TABLE products ADD COLUMN image VARCHAR(255) NULL"
    );

}


// --------------------------------------------------
// VARIABLES
// --------------------------------------------------

$error = '';


// --------------------------------------------------
// ADD PRODUCT
// --------------------------------------------------

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['add_product'])
) {

    $name =
        trim($_POST['name'] ?? '');

    $description =
        trim($_POST['description'] ?? '');

    $price =
        filter_var(
            $_POST['price'] ?? null,
            FILTER_VALIDATE_FLOAT
        );

    $imagePath = null;


    // ----------------------------------------------
    // VALIDATE PRODUCT
    // ----------------------------------------------

    if (
        $name === ''
        || $description === ''
        || $price === false
        || $price < 0
    ) {

        $error =
            'Enter a product name, description, and a valid price.';

    }


    // ----------------------------------------------
    // IMAGE UPLOAD
    // ----------------------------------------------

    elseif (
        isset($_FILES['image'])
        && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        $upload = $_FILES['image'];


        if (
            $upload['error'] !== UPLOAD_ERR_OK
            || $upload['size'] > 5 * 1024 * 1024
        ) {

            $error =
                'Choose an image smaller than 5 MB.';

        }

        else {

            $mime =
                (new finfo(FILEINFO_MIME_TYPE))
                ->file($upload['tmp_name']);


            $extensions = [

                'image/jpeg' => 'jpg',

                'image/png' => 'png',

                'image/webp' => 'webp'

            ];


            if (!isset($extensions[$mime])) {

                $error =
                    'Use a JPG, PNG, or WebP image.';

            }

            else {

                $directory =
                    __DIR__ . '/uploads/products';


                if (
                    !is_dir($directory)
                    && !mkdir($directory, 0755, true)
                ) {

                    $error =
                        'The image upload folder could not be created.';

                }

                else {

                    $filename =
                        bin2hex(random_bytes(16))
                        . '.'
                        . $extensions[$mime];


                    if (
                        move_uploaded_file(
                            $upload['tmp_name'],
                            $directory . '/' . $filename
                        )
                    ) {

                        $imagePath =
                            'uploads/products/' . $filename;

                    }

                    else {

                        $error =
                            'The image could not be saved. Check folder permissions.';

                    }

                }

            }

        }

    }


    // ----------------------------------------------
    // INSERT PRODUCT
    // ----------------------------------------------

    if ($error === '') {

        $stmt = $pdo->prepare(
            'INSERT INTO products
            (name, description, price, image)
            VALUES (?, ?, ?, ?)'
        );


        $stmt->execute([
            $name,
            $description,
            $price,
            $imagePath
        ]);


        header(
            'Location: products.php?added=1'
        );

        exit;

    }

}


// --------------------------------------------------
// DELETE PRODUCT
// --------------------------------------------------

if (isset($_GET['delete'])) {

    $id =
        (int)$_GET['delete'];


    // Get image first
    $stmt =
        $pdo->prepare(
            'SELECT image FROM products WHERE id = ?'
        );

    $stmt->execute([$id]);

    $product =
        $stmt->fetch();


    // Delete product
    $stmt =
        $pdo->prepare(
            'DELETE FROM products WHERE id = ?'
        );

    $stmt->execute([$id]);


    // Delete image from server
    if (
        $product
        && !empty($product['image'])
    ) {

        $imageFile =
            __DIR__ . '/' . $product['image'];


        if (
            is_file($imageFile)
        ) {

            unlink($imageFile);

        }

    }


    header(
        'Location: products.php?deleted=1'
    );

    exit;

}


// --------------------------------------------------
// GET ALL PRODUCTS
// --------------------------------------------------

$products =
    $pdo
    ->query(
        'SELECT *
         FROM products
         ORDER BY id DESC'
    )
    ->fetchAll();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Manage Products | Brew Haven
    </title>

    <link
        rel="stylesheet"
        href="style.css"
    >

    <style>

        .product-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .edit-btn {
            text-decoration: none;
            font-weight: 600;
            color: #5b4033;
        }

        .edit-btn:hover {
            text-decoration: underline;
        }

        .remove {
            cursor: pointer;
        }

        .admin-product-row img {
            object-fit: cover;
        }

    </style>

</head>


<body>


<nav class="navbar">

    <a
        class="logo"
        href="index.php"
    >
        ☕ Brew Haven
    </a>


    <div class="nav-links">

        <a href="admin_index.php">
            Dashboard
        </a>

        <a
            class="active"
            href="products.php"
        >
            Products
        </a>

        <a href="orders.php">
            Orders
        </a>

        <a href="menu.php">
            View shop ↗
        </a>

    </div>

</nav>


<main class="container admin-layout">


    <!-- PAGE HEADER -->

    <div class="section-heading">

        <div>

            <span class="eyebrow">
                YOUR CATALOG
            </span>

            <h1>
                Manage products
            </h1>

            <p>
                Add, edit, or remove drinks from your menu.
            </p>

        </div>


        <a
            class="btn btn-light"
            href="menu.php"
        >
            View menu ↗
        </a>

    </div>


    <!-- MESSAGES -->

    <?php if ($error !== ''): ?>

        <div class="error">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <?php if (isset($_GET['added'])): ?>

        <div class="notice">

            Product added to your menu.

        </div>

    <?php endif; ?>


    <?php if (isset($_GET['updated'])): ?>

        <div class="notice">

            Product updated successfully.

        </div>

    <?php endif; ?>


    <?php if (isset($_GET['deleted'])): ?>

        <div class="notice">

            Product removed.

        </div>

    <?php endif; ?>


    <section class="admin-grid">


        <!-- ADD PRODUCT -->

        <form
            class="form-container product-form"
            method="POST"
            enctype="multipart/form-data"
        >

            <span class="eyebrow">
                NEW ITEM
            </span>

            <h2>
                Add a product
            </h2>


            <label for="name">
                Product name
            </label>

            <input
                id="name"
                type="text"
                name="name"
                placeholder="e.g. Vanilla latte"
                required
            >


            <label for="description">
                Description
            </label>

            <textarea
                id="description"
                name="description"
                rows="3"
                placeholder="What makes this drink special?"
                required
            ></textarea>


            <label for="price">
                Price (₱)
            </label>

            <input
                id="price"
                type="number"
                name="price"
                min="0"
                step="0.01"
                placeholder="150.00"
                required
            >


            <label for="image">

                Product photo

                <span class="label-note">
                    Optional · JPG, PNG, WebP · up to 5 MB
                </span>

            </label>


            <input
                id="image"
                type="file"
                name="image"
                accept="image/jpeg,image/png,image/webp"
            >


            <button
                type="submit"
                name="add_product"
                class="btn"
            >

                Add to menu

                <span>
                    →
                </span>

            </button>

        </form>


        <!-- PRODUCT LIST -->

        <section class="catalog-panel">


            <div class="panel-heading">

                <div>

                    <span class="eyebrow">
                        LIVE CATALOG
                    </span>

                    <h2>
                        Your products
                    </h2>

                </div>


                <span class="count-pill">

                    <?= count($products) ?>

                    items

                </span>

            </div>


            <?php if (!$products): ?>


                <div class="empty-state">

                    <span>
                        ☕
                    </span>

                    <h3>
                        Your menu is ready for its first drink
                    </h3>

                    <p>
                        Use the form to add a product.
                    </p>

                </div>


            <?php else: ?>


                <div class="admin-product-list">


                    <?php foreach ($products as $product): ?>


                        <article
                            class="admin-product-row"
                        >


                            <!-- IMAGE -->

                            <?php

                            $imageFile =
                                __DIR__ . '/' . $product['image'];

                            ?>


                            <?php if (
                                !empty($product['image'])
                                && is_file($imageFile)
                            ): ?>


                                <img
                                    src="<?= htmlspecialchars(
                                        $product['image']
                                    ) ?>"
                                    alt="<?= htmlspecialchars(
                                        $product['name']
                                    ) ?>"
                                >


                            <?php else: ?>


                                <div
                                    class="image-placeholder small-placeholder"
                                >
                                    ☕
                                </div>


                            <?php endif; ?>


                            <!-- INFORMATION -->

                            <div
                                class="admin-product-info"
                            >

                                <strong>

                                    <?= htmlspecialchars(
                                        $product['name']
                                    ) ?>

                                </strong>


                                <span>

                                    <?= htmlspecialchars(
                                        $product['description']
                                    ) ?>

                                </span>

                            </div>


                            <!-- PRICE -->

                            <strong class="price">

                                ₱<?= number_format(
                                    (float)$product['price'],
                                    2
                                ) ?>

                            </strong>


                            <!-- ACTIONS -->

                            <div
                                class="product-actions"
                            >

                                <a
                                    class="edit-btn"
                                    href="edit_product.php?id=<?= (int)$product['id'] ?>"
                                >
                                    Edit
                                </a>


                                <a
                                    class="remove"
                                    href="products.php?delete=<?= (int)$product['id'] ?>"
                                    onclick="return confirm('Remove this product?')"
                                >
                                    Delete
                                </a>

                            </div>


                        </article>


                    <?php endforeach; ?>


                </div>


            <?php endif; ?>


        </section>


    </section>


</main>


</body>

</html>