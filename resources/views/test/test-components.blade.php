@extends('layouts.admin.layout.index')

@section('title', 'Test Components - ')

@section('content')
    <nav style="--bs-breadcrumb-divider: '-';" aria-label="breadcrumb" class="my-4 bg-white rounded px-3 py-2">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item">
                <a href="#button">Button</a>
            </li>
            <li class="breadcrumb-item">
                <a href="#card-data">Card Data</a>
            </li>
            <li class="breadcrumb-item">
                <a href="#input">Input</a>
            </li>
            <li class="breadcrumb-item">
                <a href="#select">Select</a>
            </li>
            <li class="breadcrumb-item">
                <a href="#modal">Modal</a>
            </li>
            <li class="breadcrumb-item">
                <a href="#table">Table</a>
            </li>
            <li class="breadcrumb-item">
                <a href="#toast">Toast</a>
            </li>
            <li class="breadcrumb-item">
                <a href="#validate">Validate</a>
            </li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-12" id="button">
            <div class="box">
                <div class="box-body">
                    <h1 class="box-title">Button</h1>

                    <div class="mt-3">
                        <h5>Button Main</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <x-button color="primary" label="Button Primary" />
                            <x-button color="secondary" label="Button Secondary" />
                            <x-button color="info" label="Button Info" />
                            <x-button color="success" label="Button Success" />
                            <x-button color="danger" label="Button Danger" />
                            <x-button color="warning" label="Button Warning" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Button With Link</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <x-button color="primary" link="/" label="Button Primary" />
                            <x-button color="secondary" link="/" label="Button Secondary" />
                            <x-button color="info" link="/" label="Button Info" />
                            <x-button color="success" link="/" label="Button Success" />
                            <x-button color="danger" link="/" label="Button Danger" />
                            <x-button color="warning" link="/" label="Button Warning" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Button With Data Feather Icon</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <x-button color="primary" icon="check-circle" label="Button Primary" />
                            <x-button color="secondary" icon="check-circle" label="Button Secondary" />
                            <x-button color="info" icon="check-circle" label="Button Info" />
                            <x-button color="success" icon="check-circle" label="Button Success" />
                            <x-button color="danger" icon="check-circle" label="Button Danger" />
                            <x-button color="warning" icon="check-circle" label="Button Warning" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Button Sizing</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <x-button color="primary" size="lg" label="Button Primary" />
                            <x-button color="secondary" size="lg" label="Button Secondary" />
                            <x-button color="info" size="lg" label="Button Info" />
                            <x-button color="success" size="lg" label="Button Success" />
                            <x-button color="danger" size="lg" label="Button Danger" />
                            <x-button color="warning" size="lg" label="Button Warning" />

                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="d-flex flex-wrap gap-2">
                            <x-button color="primary" size="md" label="Button Primary" />
                            <x-button color="secondary" size="md" label="Button Secondary" />
                            <x-button color="info" size="md" label="Button Info" />
                            <x-button color="success" size="md" label="Button Success" />
                            <x-button color="danger" size="md" label="Button Danger" />
                            <x-button color="warning" size="md" label="Button Warning" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="d-flex flex-wrap gap-2">
                            <x-button color="primary" size="sm" label="Button Primary" />
                            <x-button color="secondary" size="sm" label="Button Secondary" />
                            <x-button color="info" size="sm" label="Button Info" />
                            <x-button color="success" size="sm" label="Button Success" />
                            <x-button color="danger" size="sm" label="Button Danger" />
                            <x-button color="warning" size="sm" label="Button Warning" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Button Rounded</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <x-button color="primary" rounded label="Button Primary" />
                            <x-button color="secondary" rounded label="Button Secondary" />
                            <x-button color="info" rounded label="Button Info" />
                            <x-button color="success" rounded label="Button Success" />
                            <x-button color="danger" rounded label="Button Danger" />
                            <x-button color="warning" rounded label="Button Warning" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Button Disabled</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <x-button color="primary" disabled label="Button Primary" />
                            <x-button color="secondary" disabled label="Button Secondary" />
                            <x-button color="info" disabled label="Button Info" />
                            <x-button color="success" disabled label="Button Success" />
                            <x-button color="danger" disabled label="Button Danger" />
                            <x-button color="warning" disabled label="Button Warning" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Button Outline</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <x-button color="primary" outline label="Button Primary" />
                            <x-button color="secondary" outline label="Button Secondary" />
                            <x-button color="info" outline label="Button Info" />
                            <x-button color="success" outline label="Button Success" />
                            <x-button color="danger" outline label="Button Danger" />
                            <x-button color="warning" outline label="Button Warning" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Button Soft</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <x-button color="primary" soft label="Button Primary" />
                            <x-button color="secondary" soft label="Button Secondary" />
                            <x-button color="info" soft label="Button Info" />
                            <x-button color="success" soft label="Button Success" />
                            <x-button color="danger" soft label="Button Danger" />
                            <x-button color="warning" soft label="Button Warning" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Button Gradient</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <x-button color="primary" gradient label="Button Primary" />
                            <x-button color="secondary" gradient label="Button Secondary" />
                            <x-button color="info" gradient label="Button Info" />
                            <x-button color="success" gradient label="Button Success" />
                            <x-button color="danger" gradient label="Button Danger" />
                            <x-button color="warning" gradient label="Button Warning" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Button Text Color</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <x-button color="primary" text="dark" label="Button Primary" />
                            <x-button color="secondary" text="dark" label="Button Secondary" />
                            <x-button color="info" text="dark" label="Button Info" />
                            <x-button color="success" text="dark" label="Button Success" />
                            <x-button color="danger" text="dark" label="Button Danger" />
                            <x-button color="warning" text="dark" label="Button Warning" />
                        </div>
                        <p class="text-danger">Support all color bootstrap</p>
                    </div>

                    <div class="mt-3">
                        <h5>Button Type</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <x-button type="submit" color="primary" label="Button Primary" />
                            <x-button type="submit" color="secondary" label="Button Secondary" />
                            <x-button type="submit" color="info" label="Button Info" />
                            <x-button type="submit" color="success" label="Button Success" />
                            <x-button type="submit" color="danger" label="Button Danger" />
                            <x-button type="submit" color="warning" label="Button Warning" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Button On Javascript Event (click & change)</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <x-button color="primary" onchange="" onclick="" label="Button Primary" />
                            <x-button color="secondary" onchange="" onclick="" label="Button Secondary" />
                            <x-button color="info" onchange="" onclick="" label="Button Info" />
                            <x-button color="success" onchange="" onclick="" label="Button Success" />
                            <x-button color="danger" onchange="" onclick="" label="Button Danger" />
                            <x-button color="warning" onchange="" onclick="" label="Button Warning" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Button Class And Id</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <x-button color="primary" id="btn-primary" label="Button Primary" />
                            <x-button color="secondary" id="btn-secondary" label="Button Secondary" />
                            <x-button color="info" id="btn-info" label="Button Info" />
                            <x-button color="success" id="btn-success" label="Button Success" />
                            <x-button color="danger" id="btn-danger" label="Button Danger" />
                            <x-button color="warning" id="btn-warning" label="Button Warning" />
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12" id="card-data">
            <div class="box">
                <div class="box-body">
                    <h1 class="box-title">Card Data</h1>
                </div>
            </div>
        </div>

        <div class="col-12" id="input">
            <div class="box">
                <div class="box-body">
                    <h1 class="box-title">Input</h1>

                    <div class="mt-3">
                        <h5>Input Type</h5>
                        <div class="row space-x-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="text" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="number" label="number" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="email" label="email" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="url" label="url" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="tel" label="tel" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="search" label="search" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input class="datepicker-input" label="date" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="file" label="file" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="time" label="time" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="datetime-local" label="datetime-local" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="week" label="week" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="month" label="month" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="year" label="year" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input-radio type="radio" label="radio" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="color" label="color" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="hidden" label="hidden" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Input Required</h5>
                        <div class="row space-x-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="required" required />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Input Autofocus</h5>
                        <div class="row space-x-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="autofocus" autofocus />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Input Name</h5>
                        <div class="row space-x-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="Name" name="" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" name="Name" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Input Value</h5>
                        <div class="row space-x-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="Value" value="Test" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Input id</h5>
                        <div class="row space-x-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="id" id="custom_id" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Input Placeholder</h5>
                        <div class="row space-x-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="placeholder" placeholder="Placeholder" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Input Autocomplete</h5>
                        <div class="row space-x-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="autocomplete" autocomplete="" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Input Disabled</h5>
                        <div class="row space-x-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="disabled" disabled />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Input size</h5>
                        <div class="row space-x-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="SM" size="sm" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="MD" size="md" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="LG" size="lg" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Input Rounded</h5>
                        <div class="row space-x-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="rounded" rounded />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Input Text Color</h5>
                        <div class="row space-x-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="text color" textColor="danger" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Input Right Icon</h5>
                        <div class="row space-x-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="right icon" rightIcon="user" classIcon="text-danger" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Input Left Icon</h5>
                        <div class="row space-x-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="left icon" leftIcon="user" classIcon="text-info" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Input Hide Asterix</h5>
                        <div class="row space-x-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="hide asterix" hideAsterix="{{ true }}" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Input Event (change & click & keyup)</h5>
                        <div class="row space-x-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="Event" onchange="" onclick="" onkeyup="" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Input Checkbox</h5>
                        <div class="row space-x-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input-checkbox label="checkbox" checked name="check" id="checkbox-1" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input-checkbox label="checkbox-2" required name="check-2" id="checkbox-2" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input-checkbox label="checkbox-3" required name="check-3" textColor="danger" color="danger" hideAsterix="1" id="checkbox-3" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Input Radio</h5>
                        <div class="row space-x-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input-radio label="radio" checked name="radio" id="radio-1" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input-radio label="radio" checked name="radio" id="radio-2" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input-radio label="radio-2" required name="radio-2" id="radio-3" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input-radio label="radio-2" required name="radio-2" id="radio-4" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input-radio label="radio-3" required name="radio-3" textColor="danger" color="danger" hideAsterix="1" id="radio-5" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input-radio label="radio-3" required name="radio-3" textColor="danger" color="danger" hideAsterix="1" id="radio-6" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12" id="select">
            <div class="box">
                <div class="box-body">
                    <h1 class="box-title">Select Input</h1>

                    <div class="mt-3">
                        <h5>Select Default</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <x-select id="" name="" label="Select Data" required autofocus hideAsterix="true" selectType="default">
                                    <option value="Data 1">Data 1</option>
                                    <option value="Data 2">Data 2</option>
                                    <option value="Data 3">Data 3</option>
                                    <option value="Data 4">Data 4</option>
                                    <option value="Data 5">Data 5</option>
                                    <option value="Data 6">Data 6</option>
                                    <option value="Data 7">Data 7</option>
                                    <option value="Data 8">Data 8</option>
                                    <option value="Data 9">Data 9</option>
                                    <option value="Data 10">Data 10</option>
                                </x-select>
                            </div>
                            <div class="col-md-4">
                                <x-select id="" name="" label="Select Data" required selectType="default">
                                    <option value="Data 1">Data 1</option>
                                    <option value="Data 2">Data 2</option>
                                    <option value="Data 3">Data 3</option>
                                    <option value="Data 4">Data 4</option>
                                    <option value="Data 5">Data 5</option>
                                    <option value="Data 6">Data 6</option>
                                    <option value="Data 7">Data 7</option>
                                    <option value="Data 8">Data 8</option>
                                    <option value="Data 9">Data 9</option>
                                    <option value="Data 10">Data 10</option>
                                </x-select>
                            </div>
                            <div class="col-md-4">
                                <x-select id="" name="" label="Select Data" textColor="info" selectType="default">
                                    <option value="Data 1">Data 1</option>
                                    <option value="Data 2">Data 2</option>
                                    <option value="Data 3">Data 3</option>
                                    <option value="Data 4">Data 4</option>
                                    <option value="Data 5">Data 5</option>
                                    <option value="Data 6">Data 6</option>
                                    <option value="Data 7">Data 7</option>
                                    <option value="Data 8">Data 8</option>
                                    <option value="Data 9">Data 9</option>
                                    <option value="Data 10">Data 10</option>
                                </x-select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Select 2</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <x-select id="" name="" label="Select Data" required autofocus hideAsterix="true">
                                    <option value="Data 1">Data 1</option>
                                    <option value="Data 2">Data 2</option>
                                    <option value="Data 3">Data 3</option>
                                    <option value="Data 4">Data 4</option>
                                    <option value="Data 5">Data 5</option>
                                    <option value="Data 6">Data 6</option>
                                    <option value="Data 7">Data 7</option>
                                    <option value="Data 8">Data 8</option>
                                    <option value="Data 9">Data 9</option>
                                    <option value="Data 10">Data 10</option>
                                </x-select>
                            </div>
                            <div class="col-md-4">
                                <x-select id="" name="" label="Select Data" required>
                                    <option value="Data 1">Data 1</option>
                                    <option value="Data 2">Data 2</option>
                                    <option value="Data 3">Data 3</option>
                                    <option value="Data 4">Data 4</option>
                                    <option value="Data 5">Data 5</option>
                                    <option value="Data 6">Data 6</option>
                                    <option value="Data 7">Data 7</option>
                                    <option value="Data 8">Data 8</option>
                                    <option value="Data 9">Data 9</option>
                                    <option value="Data 10">Data 10</option>
                                </x-select>
                            </div>
                            <div class="col-md-4">
                                <x-select id="" name="" label="Select Data" textColor="info">
                                    <option value="Data 1">Data 1</option>
                                    <option value="Data 2">Data 2</option>
                                    <option value="Data 3">Data 3</option>
                                    <option value="Data 4">Data 4</option>
                                    <option value="Data 5">Data 5</option>
                                    <option value="Data 6">Data 6</option>
                                    <option value="Data 7">Data 7</option>
                                    <option value="Data 8">Data 8</option>
                                    <option value="Data 9">Data 9</option>
                                    <option value="Data 10">Data 10</option>
                                </x-select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12" id="modal">
            <div class="box">
                <div class="box-body">
                    <h1 class="box-title">Modal</h1>

                    <div class="mt-3">
                        <h5></h5>

                        <x-button color="primary" dataToggle="modal" dataTarget="#test-modal" label="Show Detail" />
                        <x-modal title="Modal Title" id="test-modal">
                            <x-slot name="modal_body">
                                <div class="form-group">
                                    <x-input type="text" label="text" />
                                </div>
                                <div class="form-group">
                                    <x-input type="text" label="text" />
                                </div>
                                <div class="form-group">
                                    <x-input type="text" label="text" />
                                </div>
                            </x-slot>
                            <x-slot name="modal_footer">
                                <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                                <x-button type="button" color="primary" label="Save" />
                            </x-slot>
                        </x-modal>

                        <x-button color="primary" dataToggle="modal" dataTarget="#test-modal-2" label="Show Detail" />
                        <x-modal title="Modal Title" id="test-modal-2" headerColor="danger">
                            <x-slot name="modal_body">
                                <div class="form-group">
                                    <x-input type="text" label="text" />
                                </div>
                                <div class="form-group">
                                    <x-input type="text" label="text" />
                                </div>
                                <div class="form-group">
                                    <x-input type="text" label="text" />
                                </div>
                            </x-slot>
                            <x-slot name="modal_footer">
                                <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                                <x-button type="button" color="primary" label="Save" />
                            </x-slot>
                        </x-modal>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12" id="table">
            <div class="box">
                <div class="box-body">
                    <h1 class="box-title">Table</h1>

                    <div class="mt-3">
                        <h5></h5>
                        <x-table>
                            <x-slot name="table_head">
                                <th>#</th>
                                <th>{{ Str::headline('data') }}</th>
                                <th>{{ Str::headline('data') }}</th>
                                <th>{{ Str::headline('data') }}</th>
                                <th></th>
                            </x-slot>
                            <x-slot name="table_body">
                                @for ($i = 1; $i <= 10; $i++)
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>Data 1</td>
                                        <td>Data 1</td>
                                        <td>Data 1</td>
                                        <td>
                                            <x-button color="primary" label="Show Detail" />
                                            <x-button color="warning" label="Edit" />
                                            <x-button color="danger" label="Delete" dataTarget="#delete-{{ $i }}" dataToggle="modal" />
                                            <x-modal-delete id="delete-{{ $i }}" url="admin.user.index" />
                                        </td>
                                    </tr>
                                @endfor
                            </x-slot>
                        </x-table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12" id="toast">
            <div class="box">
                <div class="box-body">
                    <h1 class="box-title">Toast / Notification</h1>

                    <div class="mt-3">
                        <h5>Ubah di file untuk melihat contoh</h5>
                        <div class="toast-container top-0 end-0 p-3" style="position: fixed!important;">
                            {{-- <x-toast />
                            <x-toast />
                            <x-toast /> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12" id="validate">
            <div class="box">
                <div class="box-body">
                    <h1 class="box-title">Validate Error</h1>

                    <div class="mt-3">
                        <h5>Ubah di file untuk melihat contoh</h5>
                        <x-validate-error />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12" id="table">
            <div class="box">
                <div class="box-body">
                    <h1 class="box-title">Table</h1>

                    <div class="mt-3">
                        <h5></h5>
                        <x-table>
                            <x-slot name="table_head">
                                <th>#</th>
                                <th>{{ Str::headline('data') }}</th>
                                <th>{{ Str::headline('data') }}</th>
                                <th>{{ Str::headline('data') }}</th>
                                <th></th>
                            </x-slot>
                            <x-slot name="table_body">
                                @for ($i = 1; $i <= 10; $i++)
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>Data 1</td>
                                        <td>Data 1</td>
                                        <td>Data 1</td>
                                        <td>
                                            <x-button color="primary" label="Show Detail" id="detail-{{ $i }}" />
                                            <x-button color="warning" label="Edit" />
                                            <x-button color="danger" label="Delete" dataTarget="#delete-{{ $i }}" dataToggle="modal" />
                                            <x-modal-delete id="delete-{{ $i }}" url="admin.user.index" />
                                        </td>
                                    </tr>
                                    <tr id="target-{{ $i }}" class="d-none">
                                        <td colspan="5">
                                            <table>
                                                <thead>
                                                    <th>data</th>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>data</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    @push('script')
                                        <script>
                                            $('#detail-{{ $i }}').click(function(e) {
                                                e.preventDefault();
                                                $('#target-{{ $i }}').toggleClass('d-none');
                                            });
                                        </script>
                                    @endpush
                                @endfor
                            </x-slot>
                        </x-table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
