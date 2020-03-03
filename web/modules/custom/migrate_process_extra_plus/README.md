## About

A plugin for SpaceBase spreadsheets to validate or fix or pass blank
urls. Copy-pasted and just slightly changed from Migrate Process Extra

### Extra process plugins

- **validate_link_or_fix** - Checks if a link does not return a 404 header.
  Tries to fix it by pre-pending http://  or accepts ''
  Intended for spreadsheet migrations.

### validate_link

```
  field_website:
    plugin: validate_link_or_fix
    source: website
```

