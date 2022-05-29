<!DOCTYPE html>
<html>

<head>
    <title></title>
</head>

<body>
    <div class="page-header">
        <h3>Cetak Data Anggota</h3>
    </div>
    <a href="<?= base_url('admin/laporan_anggota') ?>" class="btn btn-default btn-md">
        <span class="glyphicon glyphicon-print"></span>
        Print
    </a>
    <a class="btn btn-warning btn-md" href="<?= base_url() . 'admin/laporan_pdf_anggota' ?>">
        <span class="glyphicon glyphicon-print"></span>
        Cetak PDF
    </a>
    <br><br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover" id="table-datatable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Nama Anggota</th>
                    <th>Gender</th>
                    <th>No Telpon</th>
                    <th>Alamat</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach ($anggota as $b) {
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>          
                        <td><?= $b->username ?></td>
                        <td><?= $b->nama_anggota ?></td>
                        <td><?= $b->gender ?></td>
                        <td><?= $b->no_telp ?></td>
                        <td><?= $b->alamat ?></td>
                        <td><?= $b->email ?></td>
                    </tr>
                <?php }; ?>
            </tbody>
        </table>

        <script type="text/javascript">
            window.print();
        </script>

    </div>

</body>

</html>