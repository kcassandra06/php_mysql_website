<?php

require_once __DIR__ . "/database.php";


// --------------------------------------------------
// GET PRODUCT ID
// --------------------------------------------------

$id =
    isset($_GET['id'])
    ? (int)$_GET['id']
    : 0;


if ($id <= 0) {

    header(
        'Location: products.php'
    );

    exit;

}


// --------------------------------------------------
// GET PRODUCT
// --------------------------------------------------

$stmt = $pdo->prepare(
    'SELECT *
     FROM products
     WHERE id = ?'
);

$stmt->execute([$id]);

$product =
    $stmt->fetch();


if (!$product) {

    header(
        'Location: products.php'
    );

    exit;

}

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
        Edit Product | Brew Haven
    </title>

    <link
        rel="stylesheet"
        href="style.css"
    >


    <style>

        .edit-product-page {
            max-width: 800px;
            margin: 0 auto;
        }


        .edit-form {
            background: #fff;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 8px 30px rgba(0,0,0,.06);
        }


        .form-group {
            margin-bottom: 20px;
        }


        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }


        .form-group input,
        .form-group textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 12px 14px;
            border: 1px solid #d8d1cb;
            border-radius: 8px;
            font-family: inherit;
            font-size: 15px;
        }


        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }


        .current-image {
            width: 140px;
            height: 140px;
            object-fit: cover;
            border-radius: 10px;
            display: block;
            margin-top: 10px;
        }


        .no-image {
            margin-top: 10px;
            padding: 30px;
            width: 80px;
            text-align: center;
            background: #f5f1ed;
            border-radius: 10px;
            font-size: 30px;
        }


        .form-actions {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 25px;
        }


        .cancel-btn {
            text-decoration: none;
            color: #5b4033;
            font-weight: 600;
        }


        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #5b4033;
            text-decoration: none;
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


    <div class="edit-product-page">


        <a
            class="back-link"
            href="products.php"
        >
            ← Back to products
        </a>


        <div class="section-heading">

            <div>

                <span class="eyebrow">
                    PRODUCT SETTINGS
                </span>

                <h1>
                    Edit product
                </h1>

                <p>
                    Update the details of this product.
                </p>

            </div>

        </div>


        <form
            class="edit-form"
            action="update_product.php"
            method="POST"
            enctype="multipart/form-data"
        >


            <input
                type="hidden"
                name="id"
                value="<?= (int)$product['id'] ?>"
            >


            <!-- NAME -->

            <div class="form-group">

                <label for="name">
                    Product name
                </label>

                <input
                    id="name"
                    type="text"
                    name="name"
                    value="<?= htmlspecialchars(
                        $product['name']
                    ) ?>"
                    required
                >

            </div>


            <!-- DESCRIPTION -->

            <div class="form-group">

                <label for="description">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    required
                ><?= htmlspecialchars(
                    $product['description']
                ) ?></textarea>

            </div>


            <!-- PRICE -->

            <div class="form-group">

                <label for="price">
                    Price (₱)
                </label>

                <input
                    id="price"
                    type="number"
                    name="price"
                    min="0"
                    step="0.01"
                    value="<?= htmlspecialchars(
                        $product['price']
                    ) ?>"
                    required
                >

            </div>


            <!-- CURRENT IMAGE -->

            <div class="form-group">

                <label>
                    Current product photo
                </label>


                <?php

                $imageFile =
                    __DIR__ . '/' . $product['image'];

                ?>


                <?php if (
                    !empty($product['image'])
                    && is_file($imageFile)
                ): ?>


                    <img
                        class="current-image"
                        src="<?= htmlspecialchars(
                            $product['image']
                        ) ?>"
                        alt="<?= htmlspecialchars(
                            $product['name']
                        ) ?>"
                    >


                <?php else: ?>


                    <div class="no-image">
                        ☕
                    </div>


                <?php endif; ?>

            </div>


            <!-- NEW IMAGE -->

            <div class="form-group">

                <label for="image">

                    Change product photo

                </label>


                <input
                    id="image"
                    type="file"
                    name="image"
                    accept="image/jpeg,image/png,image/webp"
                >


                <small>
                    Leave this empty if you want to keep the current photo.
                    JPG, PNG, or WebP · maximum 5 MB.
                </small>

            </div>


            <!-- BUTTONS -->

            <div class="form-actions">


                <button
                    type="submit"
                    class="btn"
                >

                    Update product

                    <span>
                        →
                    </span>

                </button>


                <a
                    class="cancel-btn"
                    href="products.php"
                >
                    Cancel
                </a>


            </div>


        </form>


    </div>


</main>


</body>

</html>