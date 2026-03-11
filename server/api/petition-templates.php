<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
setCorsHeaders();

$method = getMethod();

try {
$db = getDbSafe();
if (!$db) {
    if ($method === 'GET') jsonResponse([
        ['id'=>'tpl_taahhut1','shortName'=>'Taşıma Taahhütnamesi','name'=>'Tehlikeli Madde Taşıma Taahhütnamesi','defaultText'=>"T.C.\nASMİRA ENERJİ LOJİSTİK A.Ş.\n\nTAAHHÜTNAME\n\nFirmamız ............................ plakalı araç ile ............................ limanından tehlikeli madde taşımacılığı yapacaktır.\n\nTaşıma sırasında;\n- ADR mevzuatına uygun davranılacaktır.\n- Araç ve ekipman kontrolü yapılmış olacaktır.\n- Şoför ehliyeti ve SRC belgesi geçerli olacaktır.\n- Acil durum ekipmanları araçta bulundurulacaktır.\n\nİş bu taahhütname tarafımızca imza altına alınmıştır.\n\nTarih: ....../....../........\nFirma Kaşe ve İmza:",'category'=>'taahhutnameler','isDefault'=>true],
        ['id'=>'tpl_taahhut2','shortName'=>'Güvenlik Taahhütnamesi','name'=>'Liman Güvenlik Taahhütnamesi','defaultText'=>"T.C.\nASMİRA ENERJİ LOJİSTİK A.Ş.\n\nGÜVENLİK TAAHHÜTNAMESİ\n\nFirmamız personeli, liman sahası içerisinde;\n- ISPS Kod kurallarına uyacaktır.\n- Güvenlik talimatlarına riayet edecektir.\n- Belirlenen güzergah dışına çıkmayacaktır.\n- Fotoğraf ve video çekimi yapmayacaktır.\n\nTarih: ....../....../........\nFirma Kaşe ve İmza:",'category'=>'taahhutnameler','isDefault'=>true],
        ['id'=>'tpl_gumruk1','shortName'=>'Gümrük Giriş Dilekçesi','name'=>'Gümrük Sahası Giriş İzni Dilekçesi','defaultText'=>"............................ GÜMRÜK MÜDÜRLÜĞÜNE\n\nKonu: Araç Giriş İzni Talebi\n\nFirmamıza ait ............................ plakalı çekici ve ............................ plakalı dorse ile gümrük sahasına giriş izni talep edilmektedir.\n\nŞoför: ............................\nTC Kimlik No: ............................\nGemi Adı: ............................\nYük Cinsi: ............................\n\nGereğini arz ederim.\n\nTarih: ....../....../........\nFirma Kaşe ve İmza:",'category'=>'gumruk-dilekceleri','isDefault'=>true],
        ['id'=>'tpl_gumruk2','shortName'=>'Transit Beyan Dilekçesi','name'=>'Transit Beyanname Düzenleme Dilekçesi','defaultText'=>"............................ GÜMRÜK MÜDÜRLÜĞÜNE\n\nKonu: Transit Beyanname Talebi\n\nAşağıda bilgileri verilen eşya için transit beyanname düzenlenmesini arz ederim.\n\nGönderici: ............................\nAlıcı: ............................\nEşya Cinsi: ............................\nMiktar: ............................\nÇıkış Gümrüğü: ............................\nVarış Gümrüğü: ............................\n\nTarih: ....../....../........\nFirma Kaşe ve İmza:",'category'=>'gumruk-dilekceleri','isDefault'=>true],
        ['id'=>'tpl_ek1_1','shortName'=>'EK-1 Formu','name'=>'Tehlikeli Madde Taşımacılığı EK-1 Formu','defaultText'=>"EK-1\nTEHLİKELİ MADDE TAŞIMACILIĞI BİLGİ FORMU\n\n1. Taşıyıcı Firma: Asmira Enerji Lojistik A.Ş.\n2. Araç Plakası: ............................\n3. Dorse Plakası: ............................\n4. Şoför Adı Soyadı: ............................\n5. Şoför TC No: ............................\n6. SRC Belge No: ............................\n7. ADR Belge No: ............................\n8. Taşınan Madde: ............................\n9. UN Numarası: ............................\n10. Miktar: ............................\n11. Yükleme Yeri: ............................\n12. Boşaltma Yeri: ............................\n13. Taşıma Tarihi: ....../....../........\n\nOnay:",'category'=>'ek-1-belgeleri','isDefault'=>true],
        ['id'=>'tpl_ek1_2','shortName'=>'EK-1 Ek Belge','name'=>'EK-1 Ek Belge Listesi','defaultText'=>"EK-1 EK BELGE LİSTESİ\n\nAşağıdaki belgeler araçta bulundurulmaktadır:\n\n☐ Araç Ruhsatı\n☐ Şoför Ehliyeti\n☐ SRC Belgesi\n☐ ADR Belgesi\n☐ Taşıma Taahhütnamesi\n☐ Sigorta Poliçesi\n☐ Muayene Belgesi\n☐ Yangın Söndürme Tüpü Belgesi\n☐ İlk Yardım Seti\n☐ Acil Durum Kartı (Tremcard)\n\nTarih: ....../....../........\nKontrol Eden:",'category'=>'ek-1-belgeleri','isDefault'=>true],
    ]);
    if ($method === 'POST') { $b = getJsonBody(); $b['id'] = $b['id'] ?? ('tpl_' . time() . rand(100,999)); jsonResponse($b, 201); }
    jsonResponse(['success'=>true,'offline'=>true]);
}

// GET
if ($method === 'GET') {
    $stmt = $db->query("SELECT id, short_name as shortName, name, default_text as defaultText, category, is_default as isDefault, created_at as createdAt FROM petition_templates ORDER BY created_at");
    jsonResponse($stmt->fetchAll());
}

// POST
if ($method === 'POST') {
    $body = getJsonBody();
    validateRequired($body, ['shortName', 'name', 'category']);
    $id = $body['id'] ?? ('tpl_' . time() . rand(100, 999));

    $stmt = $db->prepare("INSERT INTO petition_templates (id, short_name, name, default_text, category, is_default, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $id,
        $body['shortName'],
        $body['name'],
        $body['defaultText'] ?? '',
        $body['category'],
        $body['isDefault'] ?? 0,
        $body['createdAt'] ?? time()
    ]);
    jsonResponse(['id' => $id], 201);
}

// PUT
if ($method === 'PUT') {
    $body = getJsonBody();
    $id = $body['id'] ?? '';
    if (!$id) jsonResponse(['error' => 'id gerekli'], 400);

    $fieldMap = [
        'shortName' => 'short_name', 'name' => 'name',
        'defaultText' => 'default_text', 'category' => 'category',
        'isDefault' => 'is_default'
    ];

    $fields = [];
    $values = [];

    foreach ($body as $key => $val) {
        if ($key !== 'id' && isset($fieldMap[$key])) {
            $fields[] = $fieldMap[$key] . " = ?";
            $values[] = $val;
        }
    }

    if (empty($fields)) jsonResponse(['error' => 'Güncellenecek alan yok'], 400);
    $values[] = $id;

    $stmt = $db->prepare("UPDATE petition_templates SET " . implode(", ", $fields) . " WHERE id = ?");
    $stmt->execute($values);
    jsonResponse(['success' => true]);
}

// DELETE
if ($method === 'DELETE') {
    $body = getJsonBody();
    $id = $body['id'] ?? '';
    if (!$id) jsonResponse(['error' => 'id gerekli'], 400);

    $db->prepare("DELETE FROM petition_templates WHERE id = ?")->execute([$id]);
    jsonResponse(['success' => true]);
}
} catch (Exception $e) {
    errorResponse($e, 'Dilekçe şablonu hatası');
}
