/*
 * Copyright 2026.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

document.addEventListener("DOMContentLoaded", () =>
{
    /** При обновлении страницы снимаем все выделенные элементы */
    document
        .querySelectorAll(".add-all-to-edit")
        .forEach(element => { if (element.checked === true) { element.checked = false; } });
});


/** Обработчики кнопок выбора отзывов */
let $selectAllToEdit = document.querySelector("#select-all-to-edit");
let $addAllToDelete = document.querySelector("#add-all-to-delete");
let $addAllToActive = document.querySelector("#add-all-to-active");


/** Выбор из списка ответов */
$selectAllToEdit?.addEventListener("click", function() {
    // Выбрать все
    $selectAllToEdit.classList.toggle("selected");

    if($selectAllToEdit.classList.contains("selected"))
    {
        $selectAllToEdit.innerText = "Снять выбор";
        $selectAllToEdit.classList.remove("btn-outline-primary");
        $selectAllToEdit.classList.add("btn-primary");
    }
    else
    {
        $selectAllToEdit.innerText = "Выбрать все";
        $selectAllToEdit.classList.add("btn-outline-primary");
        $selectAllToEdit.classList.remove("btn-primary");
    }

    const checkboxes = document.querySelectorAll(".add-all-to-edit");


    // Выбрать все
    checkboxes.forEach(checkbox => {
        checkbox.checked = $selectAllToEdit.classList.contains("selected");
    });


    const atLeastOneChecked = Array.from(checkboxes).some(cb => cb.checked);

    if (atLeastOneChecked)
    {
        $addAllToDelete.classList.remove("d-none");
        $addAllToActive.classList.remove("d-none");
    }
    else
    {
        $addAllToDelete.classList.add("d-none");
        $addAllToActive.classList.add("d-none");
    }
});

let $checkboxesAllToEdit = document.querySelectorAll(".add-all-to-edit");


/** Скрыть или показать кнопку "Удалить выбранные" */
for ($checkboxAllToEdit of $checkboxesAllToEdit) {

    $checkboxAllToEdit?.addEventListener("click", function() {

        const checkboxes = document.querySelectorAll(".add-all-to-edit");
        const atLeastOneChecked = Array.from(checkboxes).some(cb => cb.checked);

        if (atLeastOneChecked)
        {
            $addAllToDelete.classList.remove("d-none");
            $addAllToActive.classList.remove("d-none");
        }
        else
        {
            $addAllToDelete.classList.add("d-none");
            $addAllToActive.classList.add("d-none");
        }
    });
}