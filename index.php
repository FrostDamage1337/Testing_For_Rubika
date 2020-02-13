<?php
require_once 'simple_html_dom.php';
function translit($str)
{
  $rus = array(' ', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
  $lat = array('-', 'A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
  return str_replace($rus, $lat, $str);
}

function obtain_headers($link)
{
  $link = urldecode($link);
  $html = file_get_html($link);
  $working_tag = 'h1';
  $index = [
    0 => 1,
  ];
  $working_index = 0;
  foreach ($html->find('h1,h2,h3,h4,h5,h6') as $element)
  {
    if ($working_tag != $element->tag)
    {
      if ($working_tag < $element->tag)
      {
        $index[] = 1;
        $working_index++;
      } else {
        $index[$working_index] = 1;
        $working_index--;
      }
    }
    $whitespaces = str_repeat("-", $working_index);
    $result .= '<a href="#' . translit($element->plaintext) . '">' . $whitespaces . $index[$working_index] . '. ' . $element->plaintext . '</a><br>';
    $index[$working_index]++;
    $working_tag = $element->tag;
  }
  return $result;
}
if (!isset($_POST["link"]))
{
  echo '<form method="POST">';
  echo '<input type="text" name="link" placeholder="Введите ссылку">';
  echo '<input type="submit">';
  echo '</form>';
} else {
  echo obtain_headers(urlencode($_POST["link"]));
  $categories = [
    [
      'category_id' => 1,
      'name' => 'Крепеж',
      'parent_id' => 0,
    ],
    [
      'category_id' => 2,
      'name' => 'Шурупы и болты',
      'parent_id' => 1,
    ],
    [
      'category_id' => 3,
      'name' => 'Гвозди',
      'parent_id' => 1,
    ],
    [
      'category_id' => 4,
      'name' => 'Клеи',
      'parent_id' => 1,
    ],
    [
      'category_id' => 5,
      'name' => 'Дерево',
      'parent_id' => 8,
    ],
    [
      'category_id' => 6,
      'name' => 'Доски',
      'parent_id' => 5,
    ],
    [
      'category_id' => 7,
      'name' => 'Бревна',
      'parent_id' => 5,
    ],
    [
      'category_id' => 8,
      'name' => 'Материалы',
      'parent_id' => 0,
    ],
    [
      'category_id' => 9,
      'name' => 'Кирпичи',
      'parent_id' => 10,
    ],
    [
      'category_id' => 10,
      'name' => 'Камень',
      'parent_id' => 8,
    ],
    [
      'category_id' => 11,
      'name' => 'Газоблоки',
      'parent_id' => 10,
    ],
  ];

  function get_category_tree($categories)
  {
    $new_categories = [];
    $temp_categories = [];
    foreach ($categories as $category)
    {
      $temp_category =& $temp_categories[$category["category_id"]];
      $temp_category["name"] = $category["name"];
      $temp_category["parent_id"] = $category["parent_id"];
      if ($category["parent_id"] == 0)
      {
        $new_categories[$category["category_id"]] =& $temp_category;
      } else {
        $temp_categories[$category["parent_id"]]["child"][$category["category_id"]] =& $temp_category;
      }
    }
    return $new_categories;
  }

  function convert_to_list(array $array)
  {
    $html = '<ul>' . PHP_EOL;

    foreach ($array as $value)
    {
      $html .= '<li>' . $value['name'];
      if (!empty($value['child']))
      {
        $html .= convert_to_list($value['child']);
      }
      $html .= '</li>' . PHP_EOL;
    }

    $html .= '</ul>' . PHP_EOL;

    return $html;
  }
  
  echo convert_to_list(get_category_tree($categories));
}
