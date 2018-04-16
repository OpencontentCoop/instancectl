
## Build
```bash
./vendor/bin/box build -v
mv instancectl.phar /usr/local/bin/instancectl
```

## Config

Config:
```bash
cd <document_root>
instancectl config
```

Dump current config:
```bash
cd <document_root>
instancectl config -d
```

## Generate

Generate instances.yml:
```bash
cd <document_root>
instancectl generate
```

## Clean

```bash
cd <document_root>
instancectl clean
```

## Read

```bash
cd <document_root>
instancectl get
```
