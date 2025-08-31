/*
 * Copyright 2025.  Baks.dev <admin@baks.dev>
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

executeFunc(function productReviewForm()
{
    // Получаем все rate на странице
    const rates = document.querySelectorAll('.rate');
    rates.forEach(rate => {
        const checks = Array.from(rate.querySelectorAll('.form-check'));
        let $selected = -1;

        function highlight(index, className) {
            checks.forEach((check, i) => {
                const label = check.querySelector('label');
                if (i <= index) {
                    label.classList.add(className);
                } else {
                    label.classList.remove(className);
                }
            });
        }

        function clearHover() {
            checks.forEach(check => check.querySelector('label').classList.remove('hovered'));
        }

        function clearActive() {
            checks.forEach(check => check.querySelector('label').classList.remove('active'));
        }

        checks.forEach((check, i) => {
            const label = check.querySelector('label');
            label.addEventListener('mouseenter', () => highlight(i, 'hovered'));
            label.addEventListener('mouseleave', clearHover);
            label.addEventListener('click', () => {
                $selected = i;
                clearActive();
                highlight(i, 'active');
            });
            check.querySelector('input').addEventListener('focus', () => highlight(i, 'hovered'));
            check.querySelector('input').addEventListener('blur', clearHover);
            check.querySelector('input').addEventListener('change', () => {
                $selected = i;
                clearActive();
                highlight(i, 'active');
            });
        });

        // Если что-то выбрано при загрузке — подсветить
        const checked = rate.querySelector('input:checked');
        if (checked) {
            const idx = checks.findIndex(check => check.querySelector('input') === checked);
            if (idx !== -1) {
                highlight(idx, 'active');
                $selected = idx;
            }
        }
    });

    return true;

});