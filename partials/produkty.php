<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../database/db_connect.php';

$kategorie_id = isset($_GET['kategorie_id']) ? $_GET['kategorie_id'] : 0;

$sql = "SELECT id, nazev, cena FROM produkty WHERE kategorie_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $kategorie_id);
$stmt->execute();
$result = $stmt->get_result();
$produkty = $result->fetch_all(MYSQLI_ASSOC);

if (isset($_GET['pridat_id'])) {
    $produkt_id = $_GET['pridat_id'];

    if (!isset($_SESSION['kosik'])) {
        $_SESSION['kosik'] = [];
    }

    if (isset($_SESSION['kosik'][$produkt_id])) {
        $_SESSION['kosik'][$produkt_id]++;
    } else {
        $_SESSION['kosik'][$produkt_id] = 1;
    }

    $kosik_pocet = 0;
    foreach ($_SESSION['kosik'] as $mnozstvi) {
        $kosik_pocet += $mnozstvi;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Produkt byl přidán do košíku!',
        'kosik_pocet' => $kosik_pocet
    ]);
    exit(); 
}

?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produkty</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
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
            <li><a href="/php/EshopDU/partials/kosik.php" class="hover:underline">Košík (<span class="kosik-pocet"><?php echo $kosik_pocet; ?></span>)
            </a></li>
            </ul>
        </nav>
    </div>
</header>

    <main class="container mx-auto p-6">
        <h2 class="text-2xl font-semibold mb-4">Produkty</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <?php foreach ($produkty as $produkt): ?>
                <div class="bg-gray-200 p-4 rounded-lg text-center shadow-md">
                    <h3 class="text-lg font-bold"><?php echo htmlspecialchars($produkt['nazev']); ?></h3>
                    <p class="text-gray-600"><?php echo number_format($produkt['cena'], 2, ',', ' ') . ' Kč'; ?></p>
                    <button class="bg-blue-600 text-white py-2 px-4 rounded-lg add-to-cart" data-produkt-id="<?php echo $produkt['id']; ?>">Přidat do košíku</button>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="bg-gray-800 text-white text-center p-4 mt-6">
        <p>&copy; 2025 Můj E-shop</p>
    </footer>

    <script>
        $(document).ready(function() {
    $('.add-to-cart').click(function() {
        var produktId = $(this).data('produkt-id');
        
        $.ajax({
            url: 'produkty.php',
            type: 'GET',
            data: { pridat_id: produktId },
            success: function(response) {
                try {
                    var data = JSON.parse(response);
                    if (data.success) {
                        alert(data.message);
                        
                        if (data.kosik_pocet !== undefined) {
                            $('.kosik-pocet').text(data.kosik_pocet);
                        }
                    } else {
                        alert('Došlo k chybě při přidávání produktu do košíku.');
                    }
                } catch (error) {
                    console.error('Chyba při zpracování odpovědi:', error);
                    alert('Došlo k chybě při zpracování odpovědi.');
                }
            },
            error: function(xhr, status, error) {
                alert('Došlo k chybě při přidávání produktu do košíku.');
            }
        });
    });
});
    </script>
</body>
</html>

