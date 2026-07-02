# PHP Image Toolkit

This README is available in English and German.  
Diese README ist auf Englisch und Deutsch verfügbar.

[English](#english) | [Deutsch](#deutsch)

---

## English

A simple PHP-based image processing toolkit for resizing, converting and watermarking images.

## Features

* resize multiple images
* convert image formats
* add a PNG watermark to JPG images
* choose image quality for converted images
* choose watermark position and opacity
* preview processed images in the browser
* open processed images in original size
* download processed images as a ZIP file
* automatic cleanup of temporary batch folders after ZIP download

## Technologies Used

* PHP
* PHP GD Library
* PHP ZipArchive
* PHP fileinfo
* HTML / CSS
* Water.css
* XAMPP
* Git / GitHub

## Files

* index.php – start page and navigation
* resize.php – resize multiple images
* convert.php – convert images to another format
* watermark.php – add a PNG watermark to JPG images
* download_zip.php – ZIP download and cleanup
* includes/ – configuration, validation, image, upload, filename, ZIP and batch helper functions
* css/ – custom styling

## Main Functions

The project currently contains three image tools:

* Bilder skalieren – resize several images while keeping the aspect ratio
* Bildformat konvertieren – convert images to JPEG, PNG, WebP or AVIF
* Wasserzeichen hinzufügen – add a PNG watermark to multiple JPG images

## Current Status

The project already has a working basic version.

Image resizing, format conversion, watermarking, preview display, ZIP download and temporary batch cleanup have already been implemented.

The application uses helper functions for validation, filename handling, image processing, ZIP creation and batch folder management.

## Planned Improvements

* improved user interface
* drag and drop upload
* CSRF protection
* support for PNG and WebP images in the watermark tool
* text watermark option
* better handling of very large images
* automatic cleanup for abandoned batch folders
* more detailed error messages
* optional live demo deployment

## Developer

László Haraszti

## Note

This project was created for learning and portfolio purposes and is still under development.

---

## Deutsch

# PHP Image Toolkit

Eine einfache PHP-basierte Anwendung zur Bildverarbeitung.

## Funktionen

* mehrere Bilder skalieren
* Bildformate konvertieren
* PNG-Wasserzeichen zu JPG-Bildern hinzufügen
* Bildqualität bei der Konvertierung auswählen
* Position und Transparenz des Wasserzeichens auswählen
* verarbeitete Bilder im Browser anzeigen
* verarbeitete Bilder in Originalgröße öffnen
* verarbeitete Bilder als ZIP-Datei herunterladen
* automatische Löschung temporärer Batch-Ordner nach dem ZIP-Download

## Verwendete Technologien

* PHP
* PHP GD Library
* PHP ZipArchive
* PHP fileinfo
* HTML / CSS
* Water.css
* XAMPP
* Git / GitHub

## Dateien

* index.php – Startseite und Navigation
* resize.php – mehrere Bilder skalieren
* convert.php – Bilder in ein anderes Format konvertieren
* watermark.php – PNG-Wasserzeichen zu JPG-Bildern hinzufügen
* download_zip.php – ZIP-Download und Bereinigung
* includes/ – Konfiguration, Validierung, Bildverarbeitung, Upload-, Dateinamen-, ZIP- und Batch-Hilfsfunktionen
* css/ – eigene CSS-Anpassungen

## Hauptfunktionen

Das Projekt enthält derzeit drei Bildwerkzeuge:

* Bilder skalieren – mehrere Bilder unter Beibehaltung des Seitenverhältnisses skalieren
* Bildformat konvertieren – Bilder in JPEG, PNG, WebP oder AVIF konvertieren
* Wasserzeichen hinzufügen – ein PNG-Wasserzeichen zu mehreren JPG-Bildern hinzufügen

## Aktueller Stand

Das Projekt verfügt bereits über eine funktionierende Basisversion.

Das Skalieren von Bildern, die Formatkonvertierung, das Hinzufügen von Wasserzeichen, die Vorschau, der ZIP-Download und die automatische Löschung temporärer Batch-Ordner sind bereits umgesetzt.

Die Anwendung verwendet Hilfsfunktionen für Validierung, Dateinamen, Bildverarbeitung, ZIP-Erstellung und Batch-Ordnerverwaltung.

## Geplante Weiterentwicklungen

* verbesserte Benutzeroberfläche
* Drag-and-drop-Upload
* CSRF-Schutz
* Unterstützung von PNG- und WebP-Bildern im Wasserzeichen-Tool
* Text-Wasserzeichen
* bessere Behandlung sehr großer Bilder
* automatische Bereinigung nicht heruntergeladener Batch-Ordner
* ausführlichere Fehlermeldungen
* optionales Live-Demo-Deployment

## Entwickler

László Haraszti

## Hinweis

Dieses Projekt wurde zu Lern- und Portfoliozwecken erstellt und wird derzeit weiterentwickelt.
