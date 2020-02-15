<p>Here are the systems information:</p>
<ul style="list-style-type: none;overflow: auto;">
    <?php use function Kelvinho\Virus\map;

    map(explode("\n", $attack->getSystemInfo()), function ($line) { ?>
        <li>
            <pre><?php echo $line; ?></pre>
        </li>
    <?php }); ?>
</ul>