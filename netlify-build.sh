#!/bin/bash

# Install dependencies
echo "Installing dependencies..."
npm install --legacy-peer-deps

# Build assets
echo "Building assets..."
npm run build

# Create the necessary directory structure
echo "Setting up public directory..."
cp -R public/* public/_dist
cp -R build/* public/_dist

echo "Build completed successfully!"
