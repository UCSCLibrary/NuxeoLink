NuxeoLink Documentation
========================

**Introduction:**

The NuxeoLink plugin for Omeka allows Omeka administrators to pull digital objects and their associated metadata from the Nuxeo-based UC Libraries Digital Collection (UCLDC) into a local Omeka instance. Access to the UCLDC credentials required to use this plugin must be obtained from the UCLDC team. Please contact them the UCLDC team directly at [ucldc@ucop.edu](mailto:ucldc@ucop.edu).

The NuxeoLink plugin should be downloaded from the UCSC Library GitHub: [https://github.com/UCSCLibrary/NuxeoLink](https://github.com/UCSCLibrary/NuxeoLink)

Please ensure that you have downloaded the most recent version of the NuxeoLink plugin.

**Configuration:**

After obtaining the necessary credentials from the UCLDC team and installing the NuxeoLink plugin in Omeka, the Omeka administrator will need to configure the NuxeoLink plugin. This requires the following pieces of information:

Nuxeo URL: [https://nuxeo.cdlib.org/Nuxeo/site/api/v1/automation](https://nuxeo.cdlib.org/Nuxeo/site/api/v1/automation)

Nuxeo username: (as given by UCLDC team)

Nuxeo password: (as given by UCLDC team)

You must also indicate whether or not you wish to use the UCLDC schema rather than Omeka default Dublin Core (more information on this can be found in the Understanding NuxeoLink Settings: Metadata section of this documentation).

**Understanding NuxeoLink Settings:**

* **Images:**By default, NuxeoLink will not decrease the resolution of images. This means that images in Omeka will be available at the same resolution as the original UCLDC object.NuxeoLink will transform TIFFs into JPGs during import. Only the JPGs will be added into your Omeka system.

* **Audio/Video:**At this time, Audio/Video objects are being handled differently from images in UCLDC. NuxeoLink does not currently support Audio/Video object.

* **Metadata:**There are two options available for handling metadata. The first option is to use the 15 "core" Dublin Core fields available by default in Omeka and the second option is to use the UCLDC metadata scheme.The default configuration of the plugin is to use the 15 “core” Dublin Core field available by default in Omeka. By default, all of the metadata in UCLDC for a given object will be cross-walked into the 15 “core” Dublin Core fields available by default in Omeka. UCLDC metadata elements are cross-walked into these Dublin Core fields based on the “DC property” identified in the [UCLDC Metadata Model](https://wiki.library.ucsf.edu/display/UCLDC/Metadata+scheme). See [this table](https://docs.google.com/spreadsheets/d/1UYml7kcOgipWZxgx27-sqUXUUThMwRf55lV8gv8f1vY/edit?usp=sharing) for the mapping between UCLDC metadata and Omeka’s 15 “core” Dublin Core elements.If you prefer to maintain the metadata elements used in the UCLDC Metadata Model, you must check the box for “Use UCLDC schema?” box in the NuxeoLink configuration page. The metadata in UCLDC for a given object will be placed into a separate Omeka element set titled “UCLDC Schema” and 14 of the Dublin Core fields (all except for Title) in the default Omeka element set titled “Dublin Core” will be left blank. Title must be added to Omeka Dublin Core due to system constraints. If you use the UCLDC Metadata Scheme for your objects, we recommend either hiding the duplicate Title element either through your theme or through the [Hide Elements plugin](http://omeka.org/add-ons/plugins/hide-elements/).It is advisable to choose carefully between these two metadata configuration options. They will apply across the entire Omeka instance and once NuxeoLink is configured, altering the metadata configuration could result in loss of metadata for objects imported through the plugin.Metadata elements can always be edited after they are imported to Omeka.

**Importing UCLDC Objects:**

1. To add objects from UCLDC, navigate to "Nuxeo Link" in the Omeka admin left nav bar. 

2. Identify the collection to which you wish to add the UCLDC objects using the drop down menu.

3. Indicate whether or not the items should be immediately "public" in Omeka using the check box

4. Find objects in UCLDC using either the search box or browsing through the file tree

 1. Using the search box: the search box is case insensitive and performs a keyword search in all metadata fields in UCLDC. You can restrict your search to objects within a specific folder by first selecting the target folder before executing the search.

 2. Browsing the file tree: the file tree displays files within the target folder. If a parent folder is selected, files in all sub-folders will be displayed.

5. Select objects for import by using the checkboxes next to the thumbnails in your search results. By default, all objects are targeted for selection. If only some of the retrieved objects are desired, click the "Select None" button to un-check all of the objects then target individual objects.

6. Omeka will import the objects in the background. You are free to leave the NuxeoLink screen during the import process. The administrator who initiated the import will receive an email upon import completion.

APPENDIX: UCLDC to Omeka’s 15 "core" Dublin Core fields

<table>
 <tr>
 <td>Omeka Standard Dublin Core Field</td>
 <td>UCLDC Field Label(s)</td>
 <td>UCLDC Field Label(s)</td>
 <td>UCLDC Field Label(s)</td>
 <td>UCLDC Field Label(s)</td>
 <td>UCLDC Field Label(s)</td>
 <td>UCLDC Field Label(s)</td>
 <td>UCLDC Field Label(s)</td>
 <td>UCLDC Field Label(s)</td>
 <td>UCLDC Field Label(s)</td>
 </tr>
 <tr>
 <td>Title</td>
 <td>Title</td>
 <td>Alternative Title</td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 </tr>
 <tr>
 <td>Subject</td>
 <td>Subjects (Names)</td>
 <td>Subjects (Topics, Functions, Occupations)</td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 </tr>
 <tr>
 <td>Description</td>
 <td>Description</td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 </tr>
 <tr>
 <td>Creator</td>
 <td>Creators</td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 </tr>
 <tr>
 <td>Source</td>
 <td>Sources</td>
 <td>Provenance</td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 </tr>
 <tr>
 <td>Publisher</td>
 <td>Publishers</td>
 <td>Campus</td>
 <td>Repository</td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 </tr>
 <tr>
 <td>Date</td>
 <td>Dates</td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 </tr>
 <tr>
 <td>Contributor</td>
 <td>Contributors</td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 </tr>
 <tr>
 <td>Rights</td>
 <td>Copyright Status</td>
 <td>Copyright Statement</td>
 <td>Copyright Jurisdiction</td>
 <td>Copyright Holders</td>
 <td>Copyright Contacts</td>
 <td>Copyright Note</td>
 <td>Licensing Statement</td>
 <td>Licensing Terms</td>
 <td>Licensing Note</td>
 </tr>
 <tr>
 <td>Relation</td>
 <td>Related Resources</td>
 <td>Collection Title</td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 </tr>
 <tr>
 <td>Format</td>
 <td>Format/Physical Description</td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 </tr>
 <tr>
 <td>Language</td>
 <td>Languages</td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 </tr>
 <tr>
 <td>Type</td>
 <td>Type</td>
 <td>Forms/Genres</td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 </tr>
 <tr>
 <td>Identifier</td>
 <td>Identifier</td>
 <td>Local Identifier</td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 </tr>
 <tr>
 <td>Coverage</td>
 <td>Temporal Coverage</td>
 <td>Physical Location</td>
 <td>Places</td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 </tr>
</table>


