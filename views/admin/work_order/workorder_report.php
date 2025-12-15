<form method="get">
    <label for="year">Tahun:</label>
    <select name="year" id="year">
        <?php $currentYear = date('Y'); ?>
        <?php for ($y = $currentYear; $y >= $currentYear - 5; $y--): ?>
            <option value="<?= $y ?>" <?= ($year == $y ? 'selected' : '') ?>><?= $y ?></option>
        <?php endfor; ?>
    </select>

    <label for="month">Bulan:</label>
    <select name="month" id="month">
        <option value="">Semua</option>
        <?php
        $monthNames = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
        foreach ($monthNames as $num=>$name): ?>
            <option value="<?= $num ?>" <?= ($month == $num ? 'selected' : '') ?>><?= $name ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn btn-primary">Filter</button>
    <a href="index.php?action=exportWorkOrder&year=<?= $year ?>&month=<?= $month ?>" class="btn btn-success">
        <i class="fas fa-file-excel"></i> Export Excel
    </a>
</form>
