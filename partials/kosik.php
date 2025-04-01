<?php
session_start();
require_once '../database/db_connect.php';

if (!isset($_SESSION['kosik'])) {
    $_SESSION['kosik'] = [];
}

if (isset($_GET['pridat_id'])) {
    $produkt_id = $_GET['pridat_id'];

    if (isset($_SESSION['kosik'][$produkt_id])) {
        $_SESSION['kosik'][$produkt_id]++;
    } else {
        $_SESSION['kosik'][$produkt_id] = 1;
    }
    header("Location: kosik.php");
    exit();
}

if (isset($_GET['odstranit_id'])) {
    $odstranit_id = $_GET['odstranit_id'];

    if (isset($_SESSION['kosik'][$odstranit_id])) {
        $_SESSION['kosik'][$odstranit_id]--;

        if ($_SESSION['kosik'][$odstranit_id] <= 0) {
            unset($_SESSION['kosik'][$odstranit_id]);
        }
    }

    header("Location: kosik.php");
    exit();
}

$kosik_produkty = [];
$celkova_cena = 0;
$celkova_cena_dph = 0;

if (!empty($_SESSION['kosik'])) {
    foreach ($_SESSION['kosik'] as $produkt_id => $mnozstvi) {

        $sql = "SELECT id, nazev, cena FROM produkty WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $produkt_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $produkt = $result->fetch_assoc();
        
        if ($produkt) {
            $produkt['mnozstvi'] = $mnozstvi;
            $kosik_produkty[] = $produkt;
            $celkova_cena += $produkt['cena'] * $mnozstvi;
            $celkova_cena_dph += ($produkt['cena'] * 1.21) * $mnozstvi;
        } else {
            unset($_SESSION['kosik'][$produkt_id]);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Košík</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
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
            <li><a href="/php/EshopDU/partials/kosik.php" class="hover:underline">Košík (<span class="kosik-pocet"><?php
                    $kosik_pocet = 0;
                    if (isset($_SESSION['kosik']) && is_array($_SESSION['kosik'])) {
                        foreach ($_SESSION['kosik'] as $mnozstvi) {
                            $kosik_pocet += $mnozstvi;
                        }
                    }
                    echo $kosik_pocet;
                ?></span>)</a></li>
            </ul>
        </nav>
    </div>
</header>

    <main class="container mx-auto p-6">
        <h2 class="text-2xl font-semibold mb-4">Košík</h2>

        <?php if (!empty($kosik_produkty)): ?>
            <table class="min-w-full table-auto bg-white shadow-md rounded-lg">
                <thead>
                    <tr>
                        <th class="py-2 px-4 text-left">Produkt</th>
                        <th class="py-2 px-4 text-left">Cena</th>
                        <th class="py-2 px-4 text-left">Počet</th>
                        <th class="py-2 px-4 text-left">Celková cena</th>
                        <th class="py-2 px-4 text-left">Cena s DPH</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kosik_produkty as $produkt): ?>
                        <tr>
                            <td class="py-2 px-4"><?php echo htmlspecialchars($produkt['nazev']); ?></td>
                            <td class="py-2 px-4"><?php echo number_format($produkt['cena'], 2, ',', ' ') . ' Kč'; ?></td>
                            <td class="py-2 px-4">
                                <a href="kosik.php?pridat_id=<?php echo $produkt['id']; ?>" class="bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">+</a>
                                <?php echo $produkt['mnozstvi']; ?>
                                <a href="kosik.php?odstranit_id=<?php echo $produkt['id']; ?>" class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">-</a>
                            </td>
                            <td class="py-2 px-4"><?php echo number_format($produkt['cena'] * $produkt['mnozstvi'], 2, ',', ' ') . ' Kč'; ?></td>
                            <td class="py-2 px-4"><?php echo number_format($celkova_cena_dph, 2, ',', ' ') . ' Kč'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="mt-4 text-xl font-semibold">
                <p>Celková cena: <?php echo number_format($celkova_cena, 2, ',', ' ') . ' Kč'; ?></p>
                <p>Cena s DPH: <?php echo number_format($celkova_cena_dph, 2, ',', ' ') . ' Kč'; ?></p>
            </div>

            <div class="mt-4">
                <a href="checkout.php" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Dokončit nákup</a>
            </div>
        <?php else: ?>
            <p>Váš košík je prázdný.</p>
        <?php endif; ?>
    </main>

    <footer class="bg-gray-800 text-white text-center p-4 mt-6">
        <p>&copy; 2025 Můj E-shop</p>
    </footer>
</body>
</html>
