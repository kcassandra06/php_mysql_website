<?php

require_once __DIR__ . "/database.php";


// --------------------------------------------------
// GET FORM DATA
// --------------------------------------------------

$id =
    isset($_POST['id'])
    ? (int)$_POST['id']
    : 0;


$name =
    trim($_POST['name'] ?? '');


$description =
    trim($_POST['description'] ?? '');


$price =
    filter_var(
        $_POST['price'] ?? null,
        FILTER_VALIDATE_FLOAT
    );


// --------------------------------------------------
// VALIDATION
// --------------------------------------------------

if (
    $id <= 0
    || $name === ''
    || $description === ''
    || $price === false
    || $price < 0
) {

    die(
        'Invalid product information.'
    );

}


// --------------------------------------------------
// GET CURRENT PRODUCT
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

    die(
        'Product not found.'
    );

}


$currentImage =
    $product['image'];

$newImage =
    $currentImage;


// --------------------------------------------------
// HANDLE NEW IMAGE
// --------------------------------------------------

if (
    isset($_FILES['image'])
    && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
) {

    $upload =
        $_FILES['image'];


    // ----------------------------------------------
    // CHECK UPLOAD ERROR
    // ----------------------------------------------

    if (
        $upload['error'] !== UPLOAD_ERR_OK
    ) {

        die(
            'The image upload failed.'
        );

    }


    // ----------------------------------------------
    // CHECK FILE SIZE
    // ----------------------------------------------

    if (
        $upload['size'] > 5 * 1024 * 1024
    ) {

        die(
            'The image must be smaller than 5 MB.'
        );

    }


    // ----------------------------------------------
    // CHECK MIME TYPE
    // ----------------------------------------------

    $mime =
        (new finfo(FILEINFO_MIME_TYPE))
        ->file($upload['tmp_name']);


    $extensions = [

        'image/jpeg' => 'jpg',

        'image/png' => 'png',

        'image/webp' => 'webp'

    ];


    if (!isset($extensions[$mime])) {

        die(
            'Use a JPG, PNG, or WebP image.'
        );

    }


    // ----------------------------------------------
    // CREATE UPLOAD DIRECTORY
    // ----------------------------------------------

    $directory =
        __DIR__ . '/uploads/products';


    if (!is_dir($directory)) {

        if (
            !mkdir(
                $directory,
                0755,
                true
            )
        ) {

            die(
                'The image upload folder could not be created.'
            );

        }

    }


    // ----------------------------------------------
    // CREATE NEW FILE NAME
    // ----------------------------------------------

    $filename =
        bin2hex(
            random_bytes(16)
        )
        . '.'
        . $extensions[$mime];


    $destination =
        $directory . '/' . $filename;


    // ----------------------------------------------
    // SAVE IMAGE
    // ----------------------------------------------

    if (
        !move_uploaded_file(
            $upload['tmp_name'],
            $destination
        )
    ) {

        die(
            'The image could not be saved. Check folder permissions.'
        );

    }


    $newImage =
        'uploads/products/' . $filename;


    // ----------------------------------------------
    // DELETE OLD IMAGE
    // ----------------------------------------------

    if (
        !empty($currentImage)
    ) {

        $oldImage =
            __DIR__ . '/' . $currentImage;


        if (
            is_file($oldImage)
        ) {

            unlink($oldImage);

        }

    }

}


// --------------------------------------------------
// UPDATE DATABASE
// --------------------------------------------------

$stmt = $pdo->prepare(
    'UPDATE products
     SET
        name = ?,
        description = ?,
        price = ?,
        image = ?
     WHERE id = ?'
);


$stmt->execute([

    $name,

    $description,

    $price,

    $newImage,

    $id

]);


// --------------------------------------------------
// REDIRECT BACK TO PRODUCTS
// --------------------------------------------------

header(
    'Location: products.php?updated=1'
);

exit;

?>