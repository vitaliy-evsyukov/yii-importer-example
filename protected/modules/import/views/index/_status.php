<span class="b-summary__item">
    <?php echo BsHtml::alert(
        BsHtml::ALERT_COLOR_SUCCESS,
        sprintf(
            'Импорт завершен успешно, %s %s',
            \Yii::t('app', 'была импортирована|были импортированы|было импортировано', $new),
            BsHtml::bold(\Yii::t('app', '{n} строка|{n} строки|{n} строк', $new))
        )
    ); ?>
</span>