<h2>Chapter Shop</h2>
<div class="articles">
    <?php
    foreach ($this->_['articles'] as $article) {
    ?>
        <div class="article">
            <div class="cell">
                <img src="data:image/jpeg;base64,<?php echo base64_encode($article['image']) ?>" title="<?php echo $article['name'] ?>" />
            </div>
            <div class="cell">
                <strong><?php echo $article['name'] ?></strong><br />
                <?php echo $article['description'] ?>
                <br /><br />
                <form method="POST" action="<?php echo SITE_ADDRESS . "?view=add_to_cart" ?>">
                    <select name="order[<?php echo $article['name'] ?>]">
                        <?php
                        $options = explode("\n", $article['options']);
                        var_dump($options);
                        foreach ($options as $option) {
                            echo "<option>" . $option . "</option>";
                        }
                        ?>
                    </select>
                    <input type="submit" value="In den Warenkorb" />
                </form>
            </div>
            <div class="cell price"><?php echo number_format((float)$article['price'], 2, ',', '.') ?> €</div>
        </div>
    <?php
    }
    ?>
</div>