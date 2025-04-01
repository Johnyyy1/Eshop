<?php
session_start();
require_once '../database/db_connect.php';


$jmeno_prijmeni = $ulice = $cislo_popisne = $psc = $mesto = '';
$doprava = 'osobni'; 
$errors = [];
$formular_odeslany = false;

$kosik_produkty = [];
$celkova_cena = 0;
$celkova_cena_dph = 0;

if (!isset($_SESSION['kosik']) || empty($_SESSION['kosik'])) {
    header("Location: kosik.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['jmeno_prijmeni'])) {
        $errors['jmeno_prijmeni'] = 'Zadejte jméno a příjmení';
    } else {
        $jmeno_prijmeni = htmlspecialchars($_POST['jmeno_prijmeni']);
    }

    if (empty($_POST['ulice'])) {
        $errors['ulice'] = 'Zadejte ulici';
    } else {
        $ulice = htmlspecialchars($_POST['ulice']);
    }

    if (empty($_POST['cislo_popisne'])) {
        $errors['cislo_popisne'] = 'Zadejte číslo popisné';
    } else if (!is_numeric($_POST['cislo_popisne'])) {
        $errors['cislo_popisne'] = 'Číslo popisné musí být číslo';
    } else {
        $cislo_popisne = htmlspecialchars($_POST['cislo_popisne']);
    }

    if (empty($_POST['mesto'])) {
        $errors['mesto'] = 'Zadejte město';
    } else {
        $mesto = htmlspecialchars($_POST['mesto']);
    }

    if (empty($_POST['psc'])) {
        $errors['psc'] = 'Zadejte PSČ';
    } else if (!preg_match('/^[0-9]{5}$/', $_POST['psc'])) {
        $errors['psc'] = 'PSČ musí být 5 číslic';
    } else {
        $psc = htmlspecialchars($_POST['psc']);
    }

    $doprava = isset($_POST['doprava']) ? $_POST['doprava'] : 'osobni';

    if (empty($errors)) {
        $_SESSION['objednavka'] = [
            'jmeno_prijmeni' => $jmeno_prijmeni,
            'ulice' => $ulice,
            'cislo_popisne' => $cislo_popisne,
            'mesto' => $mesto,
            'psc' => $psc,
            'doprava' => $doprava
        ];
        
        header("Location: souhrn.php");
        exit();
    }
    
    $formular_odeslany = true;
}

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
    <title>Dokončení objednávky</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100">
    <header class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">Můj E-shop</h1>
            <nav>
                <ul class="flex space-x-4">
                    <li><a href="../index.php" class="hover:underline">Domů</a></li>
                    <li><a href="kosik.php" class="hover:underline">Košík (<span class="kosik-pocet"><?php echo $kosik_pocet; ?></span>)</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container mx-auto p-6">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-6">Dokončení objednávky</h2>
            
            <div class="mb-6">
                <h3 class="text-xl font-semibold">Fakturační údaje</h3>
                
                <?php if ($formular_odeslany && !empty($errors)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        <strong class="font-bold">Chyba!</strong>
                        <ul class="mt-2">
                            <?php foreach ($errors as $error): ?>
                                <li class="text-sm"><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="checkout.php" method="post" class="mt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label for="jmeno_prijmeni" class="block text-sm font-medium text-gray-700">Jméno a příjmení *</label>
                            <input type="text" id="jmeno_prijmeni" name="jmeno_prijmeni" value="<?php echo $jmeno_prijmeni; ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border" required>
                        </div>

                        <div class="mb-4">
                            <label for="ulice" class="block text-sm font-medium text-gray-700">Ulice *</label>
                            <input type="text" id="ulice" name="ulice" value="<?php echo $ulice; ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border" required>
                        </div>

                        <div class="mb-4">
                            <label for="cislo_popisne" class="block text-sm font-medium text-gray-700">Číslo popisné *</label>
                            <input type="text" id="cislo_popisne" name="cislo_popisne" value="<?php echo $cislo_popisne; ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border" required>
                        </div>

                        <div class="mb-4">
                            <label for="mesto" class="block text-sm font-medium text-gray-700">Město *</label>
                            <input type="text" id="mesto" name="mesto" value="<?php echo $mesto; ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border" required>
                        </div>

                        <div class="mb-4">
                            <label for="psc" class="block text-sm font-medium text-gray-700">PSČ *</label>
                            <input type="text" id="psc" name="psc" value="<?php echo $psc; ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border" required maxlength="5" placeholder="12345">
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-2">Způsob dopravy</h3>
                        <div class="flex items-center mb-2">
                            <input type="radio" id="osobni" name="doprava" value="osobni" <?php echo $doprava === 'osobni' ? 'checked' : ''; ?> class="mr-2">
                            <label for="osobni">Osobní odběr (zdarma)</label>
                        </div>
                        <div class="flex items-center">
                            <input type="radio" id="prepravce" name="doprava" value="prepravce" <?php echo $doprava === 'prepravce' ? 'checked' : ''; ?> class="mr-2">
                            <label for="prepravce">Přepravní společnost (129 Kč)</label>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-lg font-semibold">Souhrn ceny:</h3>
                        <div class="flex justify-between mt-2">
                            <span>Cena bez DPH:</span>
                            <span id="cena-bez-dph"><?php echo number_format($celkova_cena, 2, ',', ' '); ?> Kč</span>
                        </div>
                        <div class="flex justify-between mt-1">
                            <span>DPH (21%):</span>
                            <span id="dph"><?php echo number_format($celkova_cena_dph - $celkova_cena, 2, ',', ' '); ?> Kč</span>
                        </div>
                        <div class="flex justify-between mt-1">
                            <span>Doprava:</span>
                            <span id="doprava-cena">0,00 Kč</span>
                        </div>
                        <div class="flex justify-between mt-2 text-lg font-bold">
                            <span>Celková cena s DPH:</span>
                            <span id="celkova-cena"><?php echo number_format($celkova_cena_dph, 2, ',', ' '); ?> Kč</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Pokračovat k souhrnu objednávky</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white text-center p-4 mt-6">
        <p>&copy; 2025 Můj E-shop</p>
    </footer>

    <script>
        $(document).ready(function() {
            let cenaSDPH = <?php echo $celkova_cena_dph; ?>;
            const dopravaCena = 129;
            
            function aktualizujCenu() {
                let celkovaCena = cenaSDPH;
                const vybranaDopravaElement = $('input[name="doprava"]:checked');
                
                if (vybranaDopravaElement.val() === 'prepravce') {
                    celkovaCena += dopravaCena;
                    $('#doprava-cena').text('129,00 Kč');
                } else {
                    $('#doprava-cena').text('0,00 Kč');
                }
                
                $('#celkova-cena').text(celkovaCena.toLocaleString('cs-CZ', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).replace('.', ',') + ' Kč');
            }
            
            aktualizujCenu();
            
            $('input[name="doprava"]').change(function() {
                aktualizujCenu();
            });
        });
    </script>
</body>
</html>