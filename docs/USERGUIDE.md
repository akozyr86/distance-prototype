# Distance Console Tool User Guide

## Basic concepts

Tool resolve addresses to geolocations using an api (2 service providers at the moment Google Maps API
and PositionStack.com) and calculate the distance in kilometers from each address to the Adchieve
headquarters.
After calculating each distance,the results is sorted by distance to the Adchieve
headquarters and is written to the console.
The calculations stored in a csv files named `var/engine_distances_timestamp.csv`, in the
following format:

``` 
Sortnumber,Distance,Name,Address
1,"1.23 km","test","this is just a test address, the Netherlands"
2,"2.24 km","test","this is just a test address, the Netherlands"
```

The distance will be formatted in 2 decimals with km added.

Addresses used as HeadQuarter (can be changed in config/params.yml ``headquarter_address``)

```
Sint Janssingel 92, 5211 DA 's-Hertogenbosch, The Netherlan
```

## Installation

Execute composer install command

```shell
composer install --no-dev
```

## Usage

Command: `bin/console distance:calculate csv-data-file-name [--option]`. Options:

- `csv-data-file-name` - name of the file with input csv placed in var folder
- `--map-engine` -  (-m ) map-engine usage. 2 map-engines supported at the moment Google Maps API, PositionStack.com
  "google"/"pstack"

Examples of usage:

```shell 
bin/console distance:calculate "test-input.csv" -m google
```

```shell 
bin/console distance:calculate "test-input.csv" -m pstack
```

### Configuring Api Keys.

Both Google Maps Api and PositionStack requires API. API keys should be added to the config/params.yml.
Google Maps API to `google_maps_api_key` and PositionStack to `position_stack_api_key` fields.

