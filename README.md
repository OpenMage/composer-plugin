# OpenMage Composer-Plugin

Helper to install via composer ...

- [ChartJs](https://github.com/chartjs/Chart.js)
- [flow.js](https://github.com/flowjs/flow.js)
- [jQuery](https://github.com/components/jquery)
- [TinyMCE](https://github.com/tinymce/tinymce)

## Support packages from unpkg.com

With v3 it ist possible to download files from https://unpkg.com/.

Example: add a package to composers extra section

```
        "openmage-unpkg-packages": {
            "@eastdesire/jscolor": {
                "version": "2.5.2",
                "source": "",
                "target": "js/jscolor",
                "files": [
                    "jscolor.js"
                ]
            }
        }
```

<small>Note: composer install is broken since ChartJs v3. Until its fixed we try to download from https://unpkg.com</small>

---

## License

- [OSL v3.0](http://opensource.org/licenses/OSL-3.0)
