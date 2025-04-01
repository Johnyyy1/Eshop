<?php
session_start();
require_once '../database/db_connect.php';

if (!isset($_SESSION['objednavka']) || !isset($_SESSION['kosik']) || empty($_SESSION['kosik'])) {
    header("Location: kosik.php");
    exit();
}

$objednavka = $_SESSION['objednavka'];
$jmeno_prijmeni = $objednavka['jmeno_prijmeni'];
$ulice = $objednavka['ulice'];
$cislo_popisne = $objednavka['cislo_popisne'];
$mesto = $objednavka['mesto'];
$psc = $objednavka['psc'];
$doprava = $objednavka['doprava'];

$kosik_produkty = [];
$celkova_cena = 0;
$celkova_cena_dph = 0;

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
    }
}

$doprava_cena = ($doprava === 'prepravce') ? 129 : 0;
$celkova_cena_s_dopravou = $celkova_cena + $doprava_cena;
$celkova_cena_s_dopravou_dph = $celkova_cena_dph + $doprava_cena;

$objednavka_odeslana = false;
$objednavka_cislo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sql_objednavka = "INSERT INTO objednavky (jmeno_prijmeni, ulice, cislo_popisne, mesto, psc, doprava, stav) 
    VALUES (?, ?, ?, ?, ?, ?, 'čeká na vyřízení')";
    $stmt_objednavka = $conn->prepare($sql_objednavka);
    $stmt_objednavka->bind_param("ssssss", $jmeno_prijmeni, $ulice, $cislo_popisne, $mesto, $psc, $doprava);
    
    if ($stmt_objednavka->execute()) {
        $objednavka_id = $conn->insert_id;
        $objednavka_cislo = date('Ymd') . str_pad($objednavka_id, 5, '0', STR_PAD_LEFT);
        
        $sql_polozky = "INSERT INTO objednavky_polozky (objednavka_id, produkt_id, mnozstvi, cena_kus) VALUES (?, ?, ?, ?)";
        $stmt_polozky = $conn->prepare($sql_polozky);
        
        foreach ($kosik_produkty as $produkt) {
            $stmt_polozky->bind_param("iiid", $objednavka_id, $produkt['id'], $produkt['mnozstvi'], $produkt['cena']);
            $stmt_polozky->execute();
        }
        
        unset($_SESSION['kosik']);
        unset($_SESSION['objednavka']);
        
        $objednavka_odeslana = true;
    }
}

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

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Souhrn objednávky</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <header class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">Můj E-shop</h1>
            <nav>
                <ul class="flex space-x-4">
                    <li><a href="../index.php" class="hover:underline">Domů</a></li>
                    <?php if (!$objednavka_odeslana): ?>
                    <li><a href="kosik.php" class="hover:underline">Košík (<span class="kosik-pocet"><?php echo $kosik_pocet; ?></span>)</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container mx-auto p-6">
        <?php if ($objednavka_odeslana): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg shadow-md mb-6">
        <h2 class="text-2xl font-semibold mb-2">Objednávka byla odeslána</h2>
        <p class="mb-2">Vaše objednávka byla úspěšně zpracována.</p>
        <p class="font-semibold">Číslo objednávky: <?php echo $objednavka_cislo; ?></p>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow-md">
        <p class="mb-4">Děkujeme za Váš nákup. Potvrzení o objednávce bylo odesláno na Vaši e-mailovou adresu.</p>
        <a href="../index.php" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Pokračovat v nákupu</a>
    </div>
        <?php else: ?>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-semibold mb-6">Souhrn objednávky</h2>
                
                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-2">Fakturační údaje</h3>
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <p><strong>Jméno a příjmení:</strong> <?php echo htmlspecialchars($jmeno_prijmeni); ?></p>
                        <p><strong>Adresa:</strong> <?php echo htmlspecialchars($ulice . ' ' . $cislo_popisne); ?></p>
                        <p><strong>Město:</strong> <?php echo htmlspecialchars($mesto); ?></p>
                        <p><strong>PSČ:</strong> <?php echo htmlspecialchars($psc); ?></p>
                        <p><strong>Způsob dopravy:</strong> <?php echo $doprava === 'osobni' ? 'Osobní odběr' : 'Přepravní společnost'; ?></p>
                    </div>
                </div>
                
                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-2">Položky objednávky</h3>
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b text-left">Produkt</th>
                                <th class="py-2 px-4 border-b text-right">Cena za kus</th>
                                <th class="py-2 px-4 border-b text-center">Množství</th>
                                <th class="py-2 px-4 border-b text-right">Celkem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($kosik_produkty as $produkt): ?>
                                <tr>
                                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($produkt['nazev']); ?></td>
                                    <td class="py-2 px-4 border-b text-right"><?php echo number_format($produkt['cena'], 2, ',', ' '); ?> Kč</td>
                                    <td class="py-2 px-4 border-b text-center"><?php echo $produkt['mnozstvi']; ?></td>
                                    <td class="py-2 px-4 border-b text-right"><?php echo number_format($produkt['cena'] * $produkt['mnozstvi'], 2, ',', ' '); ?> Kč</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-2">Souhrn ceny</h3>
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <div class="flex justify-between">
                            <span>Cena bez DPH:</span>
                            <span><?php echo number_format($celkova_cena, 2, ',', ' '); ?> Kč</span>
                        </div>
                        <div class="flex justify-between mt-1">
                            <span>DPH (21%):</span>
                            <span><?php echo number_format($celkova_cena_dph - $celkova_cena, 2, ',', ' '); ?> Kč</span>
                        </div>
                        <div class="flex justify-between mt-1">
                            <span>Doprava:</span>
                            <span><?php echo number_format($doprava_cena, 2, ',', ' '); ?> Kč</span>
                        </div>
                        <div class="flex justify-between mt-2 text-lg font-bold">
                            <span>Celková cena s DPH:</span>
                            <span><?php echo number_format($celkova_cena_s_dopravou_dph, 2, ',', ' '); ?> Kč</span>
                        </div>
                    </div>
                </div>
                
                <form action="souhrn.php" method="post">
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Odeslat objednávku</button>
                    <a href="checkout.php" class="px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 ml-2">Upravit údaje</a>
                </form>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-gray-800 text-white text-center p-4 mt-6">
        <p>&copy; 2025 Můj E-shop</p>
    </footer>
</body>
</html>