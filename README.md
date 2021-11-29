# TimeZone / Geocoding Research

## Reference Links
* [Geocoding](https://developers.google.com/maps/documentation/geocoding/overview)
* [RegionBiasing](https://developers.google.com/maps/documentation/geocoding/overview#RegionCodes)
* [ComponentFiltering](https://developers.google.com/maps/documentation/geocoding/overview#ComponentFiltering)
* [AddressComponentTypes](https://developers.google.com/maps/documentation/geocoding/overview#Types)


## To use or not to use a region bias or component filter

With google Geocoding you can add `Region Biases (ex. region=fr)` and `Component Filters (ex. components=country:fr)`.

The region parameter will only influence, not fully restrict, results from the geocoder.

The components filters will fully restrict the results from the geocoder.

**Note: when using these you MUST use the two letter country code, the full country name is no good.**

I DO NOT think we want to use either of these in my initial tests and the quality of the data in the root servers (not always great), google does
a better job of deciphering our garbage without this filters.

Take the following example string to geocode, which is a french meeting in the US from the french server. This is how current code would produce the location string.

`Philadelphia, United States, fr`

without any region bias or component filter we get the geographic center of `Philadelphia, PA, USA` as a result. We like this.

with region bias we get an address in paris `2 Av. Gabriel, 75008 Paris, France`. We do NOT like this.

with component filters on, we get zero results. We do NOT like this either.

TL;DR maybe we should not use these.

## Assumptions

It is pretty safe to assume that if only `country` and `administrative_area_level_1` are returned as [AddressComponentTypes](https://developers.google.com/maps/documentation/geocoding/overview#Types).
Then we probably don't have enough accuracy to feel really good about getting the right TimeZone. Except there are only 25 countries with greater than 1 timezone, so if only the
`country` is returned and its not on that list, well then we can feel pretty good about getting correct timezone.

