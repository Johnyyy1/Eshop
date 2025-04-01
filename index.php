<?php
session_start();
require_once './database/db_connect.php';

$sql_kategorie = "SELECT id, nazev FROM kategorie LIMIT 6"; 
$result_kategorie = $conn->query($sql_kategorie);
$kategorie = $result_kategorie->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php
    $kosik_pocet = isset($_SESSION['kosik']) ? count($_SESSION['kosik']) : 0;
    ?>

<?php
function spocitejKosik() {
    $pocet = 0;
    if (isset($_SESSION['kosik']) && is_array($_SESSION['kosik'])) {
        foreach ($_SESSION['kosik'] as $mnozstvi) {
            $pocet += $mnozstvi;
        }
    }
    return $pocet;
}
$kosik_pocet = spocitejKosik();
?>

<header class="bg-blue-600 text-white p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-xl font-bold">Můj E-shop</h1>
        <nav>
            <ul class="flex space-x-4">
            <li><a href="/php/EshopDU/index.php" class="hover:underline">Domů</a></li>
            <li><a href="/php/EshopDU/partials/kosik.php" class="hover:underline">Košík (<span class="kosik-pocet"><?php echo $kosik_pocet; ?></span>)</a></li>
            </ul>
        </nav>
    </div>
</header>

    <main class="container mx-auto p-6">
        <section class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4">Kategorie</h2>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <?php foreach ($kategorie as $kategorie_item): ?>
                    <div class="bg-gray-200 p-4 rounded-lg text-center shadow-md">
                        <img src="images/<?php echo $kategorie_item['id']; ?>.jpg" alt="<?php echo htmlspecialchars($kategorie_item['nazev']); ?>" class="w-full h-48 object-cover rounded-lg">
                        <h3 class="text-lg font-bold mt-4"><?php echo htmlspecialchars($kategorie_item['nazev']); ?></h3>
                        <div class="mt-4">
                            <a href="partials/produkty.php?kategorie_id=<?php echo $kategorie_item['id']; ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                Zjistit více
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <footer class="bg-gray-800 text-white text-center p-4 mt-6">
        <p>&copy; 2025 Můj E-shop</p>
    </footer>

</body>
</html>
