<h2>Data z XML feedu:</h2>

<table>
    <thead>
    </thead>
    <tbody>
        {foreach $data as $item}
            <a href="{$item.link}?utm_source=eshop&utm_medium=cofisczcofisnews-dashboard"target="_blank">{$item.description}</a><br>
        {/foreach}
    </tbody>
</table>